<?php

namespace Reedware\LaravelSeeders\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\DelegatesToResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use Reedware\LaravelSeeders\DeleteMissing;

trait BehavesAsResource
{
    use ConditionallyLoadsAttributes, DelegatesToResource;

    /**
     * The model this resource corresponds to.
     *
     * @var string
     */
    public static $model;

    /**
     * The attributes to match when creating or updating records.
     *
     * @var array
     */
    public static $match = [];

    /**
     * Whether or not to omit the primary key.
     *
     * @var bool
     */
    public static $omitPrimaryKey = true;

    /**
     * Additional columns to omit when seeding.
     *
     * @var array
     */
    public static $omit = [];

    /**
     * The columns to used order the resources in storage.
     *
     * @var array
     */
    public static $orderBy = [];

    /**
     * Whether or not new records in storage can be inserted into the database.
     *
     * @var bool
     */
    public static $allowCreating = true;

    /**
     * Whether or not existing records in storage can be updated within the database.
     *
     * @var bool
     */
    public static $allowUpdating = true;

    /**
     * Whether or not missing records in storage can be deleted from the database.
     *
     * @var bool
     */
    public static $allowDeleting = true;

    /**
     * The resource instance.
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    public $resource;

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    final public function __construct(Model $resource = null)
    {
        $this->resource = $resource ?: new static::$model;
    }

    /**
     * Returns the formatted records to be written into storage.
     *
     * @return \Illuminate\Support\Enumerable
     */
    public static function toStorage()
    {
        return static::newStorageQuery()->cursor()->map(function (Model $model) {
            return (new static($model))->serializeForStorage();
        });
    }

    /**
     * Creates and returns a new storage query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function newStorageQuery()
    {
        // Create a new query
        $query = (new static::$model)->newQueryWithoutScope(SoftDeletingScope::class);

        // Select all columns
        $query->select((new static::$model)->getTable().'.*');

        // Require the matching columns to be non-null
        foreach (static::$match as $column) {
            $query->whereNotNull($column);
        }

        // Order the columns
        foreach (static::$orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        // Return the query
        return $query;
    }

    /**
     * Prepares this resource for being written to storage.
     *
     * @return array
     */
    public function serializeForStorage()
    {
        // Determine the array data
        $data = $this->toArray();

        // Omit ignored attributes
        foreach (static::omit() as $column) {
            unset($data[$column]);
        }

        // Return the resolved array
        return $this->filter((array) $data);
    }

    /**
     * Transform this resource into an array.
     *
     * @return array
     */
    public function toArray()
    {
        return static::mapAttributes($this->resource->attributesToArray());
    }

    /**
     * Maps the model attributes to stored attributes.
     *
     * @return array
     */
    public static function mapAttributes(array $attributes)
    {
        $attributes = static::mapArrayToJson($attributes);

        $attributes = static::mapBooleanToString($attributes);

        return static::mapSoftDeletes($attributes);
    }

    /**
     * Maps array values into json.
     *
     * @return array
     */
    protected static function mapArrayToJson(array $attributes)
    {
        return array_map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }, $attributes);
    }

    /**
     * Maps boolean values into strings.
     *
     * @return array
     */
    protected static function mapBooleanToString(array $attributes)
    {
        return array_map(function ($value) {
            if ($value === true) {
                return '1';
            } elseif ($value === false) {
                return '0';
            } else {
                return $value;
            }
        }, $attributes);
    }

    /**
     * Maps the soft deletes datetime to a trashed boolean.
     *
     * @return array
     */
    protected static function mapSoftDeletes(array $attributes)
    {
        if (! static::softDeletes()) {
            return $attributes;
        }

        $column = (new static::$model)->getDeletedAtColumn();

        $attributes['trashed'] = ! is_null($attributes[$column]);

        unset($attributes[$column]);

        return $attributes;
    }

    /**
     * Fills the underlying resource table with records from storage.
     *
     * @return array
     */
    public static function fromStorage(Enumerable $records)
    {
        // If we're going to delete missing records, then we need to know
        // what we have. Rather than looping through our list multiple
        // times, we're going to be as efficient as possible here.

        // If we plan to delete missing records, prepare the tracking table
        if (static::allowDeleting()) {
            DeleteMissing::prepare(static::class);
        }

        // Initialize the create, update, and delete counts
        $creates = $updates = $deletes = 0;

        // Track creations
        (static::$model)::created(function () use (&$creates) {
            $creates++;
        });

        // Track updates
        (static::$model)::updated(function () use (&$updates) {
            $updates++;
        });

        // Update or create each record (this will also track for deletion)
        $records->each(function (array $array) {
            static::unserializeFromStorage($array)->updateOrCreate();
        });

        // If we plan to delete missing records, do so now
        if (static::allowDeleting()) {
            $deletes = DeleteMissing::finish(static::newDeleteQuery());
        }

        // Return the counts
        return [
            $creates, $updates, $deletes,
        ];
    }

    /**
     * Restores a new resource from storage using the specified array.
     *
     * @return static
     */
    public static function unserializeFromStorage(array $array)
    {
        return static::fromArray($array);
    }

    /**
     * Transforms an array into this resource.
     *
     * @return static
     */
    public static function fromArray(array $array)
    {
        return new static((new static::$model)->setRawAttributes(static::unmapAttributes($array), true));
    }

    /**
     * Unmaps stored attributes to model attributes.
     *
     * @return array
     */
    public static function unmapAttributes(array $attributes)
    {
        $attributes = static::unmapJsonToArray($attributes);

        return static::unmapSoftDeletes($attributes);
    }

    /**
     * Maps array values into json.
     *
     * @return array
     */
    protected static function unmapJsonToArray(array $attributes)
    {
        $casts = (new static::$model)->getCasts();

        foreach ($attributes as $key => &$value) {
            if (isset($casts[$key]) && in_array(strtolower($casts[$key]), ['array', 'json'])) {
                $value = json_decode($value, true);
            }
        }

        return $attributes;
    }

    /**
     * Unmaps the trashed boolean to a soft deletes datetime.
     *
     * @return array
     */
    protected static function unmapSoftDeletes(array $attributes)
    {
        if (! static::softDeletes()) {
            return $attributes;
        }

        $column = (new static::$model)->getDeletedAtColumn();

        $attributes[$column] = ($attributes['trashed'] ?? null)
            ? (new static::$model)->freshTimestamp()
            : null;

        unset($attributes['trashed']);

        return $attributes;
    }

    /**
     * Updates or creates the stored resource based on the instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate()
    {
        // Determine the resource attributes
        $attributes = $this->resource->getAttributes();

        // Determine the attributes to match against
        $match = Arr::only($attributes, array_map(function ($key) {
            return last(explode('.', $key));
        }, static::$match));

        // Omit ignored attributes
        foreach (static::omit() as $column) {
            unset($attributes[$column]);
        }

        // Construct a new model query
        $query = $this->resource->newQueryWithoutScope(SoftDeletingScope::class);

        // Find or construct a new model
        $instance = $query->firstOrNew($match, $attributes);

        // Prevent double soft deletion
        if (static::softDeletes() && $instance->trashed() && ! is_null($attributes[$instance->getDeletedAtColumn()])) {
            unset($attributes[$instance->getDeletedAtColumn()]);
        }

        // Update and persist the changes
        if ($this->allowUpdateOrCreate($instance)) {
            $instance->forceFill($attributes)->save();
        }

        // If we plan to delete missing records, keep the one we found
        if (static::allowDeleting()) {
            DeleteMissing::keep(static::class, $instance);
        }

        // Return the model instance
        return $instance;
    }

    /**
     * Returns whether or not the specified instance can be updated or created.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @return bool
     */
    protected function allowUpdateOrCreate($instance)
    {
        return (! $instance->exists && static::allowCreating())
            || ($instance->exists && static::allowUpdating());
    }

    /**
     * Returns whether or not the associated model soft deletes.
     *
     * @return bool
     */
    public static function softDeletes()
    {
        return (new static::$model) instanceof Model
            && in_array(SoftDeletes::class, class_uses_recursive(static::$model));
    }

    /**
     * Returns whether or not to omit the primary key.
     *
     * @return bool
     */
    public static function omitPrimaryKey()
    {
        return static::$omitPrimaryKey;
    }

    /**
     * Returns the columns to omit when seeding.
     *
     * @return array
     */
    public static function omit()
    {
        // Create a model instance for reference
        $instance = new static::$model;

        // Omit the meta columns
        return array_merge(static::$omit, array_filter([
            static::omitPrimaryKey() ? $instance->getKeyName() : null,
            $instance->getCreatedAtColumn(),
            $instance->getUpdatedAtColumn(),
        ]));
    }

    /**
     * Returns whether or not new records in storage can be inserted into the database.
     *
     * @return bool
     */
    public static function allowCreating()
    {
        return static::$allowCreating;
    }

    /**
     * Returns whether or not existing records in storage can be updated within the database.
     *
     * @return bool
     */
    public static function allowUpdating()
    {
        return static::$allowUpdating;
    }

    /**
     * Returns whether or not missing records in storage can be deleted from the database.
     *
     * @return bool
     */
    public static function allowDeleting()
    {
        return static::$allowDeleting;
    }

    /**
     * Creates and returns a new delete query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function newDeleteQuery()
    {
        return DeleteMissing::query(static::class);
    }
}
