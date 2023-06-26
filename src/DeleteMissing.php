<?php

namespace Reedware\LaravelSeeders;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DeleteMissing
{
    /**
     * The repository for tracking resources.
     */
    protected static ResourceRepository $repository;

    /**
     * The repository resolver.
     */
    protected static ?Closure $resolver = null;

    /**
     * Prepares the environment for deleting missing records.
     */
    public static function prepare(string $resource): void
    {
        if (! isset(static::$repository)) {
            static::$repository = static::resolveRepository();
        }
    }

    /**
     * Resolves the resource repository.
     */
    protected static function resolveRepository(): ?ResourceRepository
    {
        if (is_null(static::$resolver)) {
            return null;
        }

        return (static::$resolver)();
    }

    /**
     * Marks the specified model to be kept after pruning.
     */
    public static function keep(string $resource, Model $instance): void
    {
        static::$repository->add($resource, $instance);
    }

    /**
     * Deletes the missing records and cleans up the environment.
     */
    public static function finish(Builder $query): int
    {
        // Create a query identifying the missing records
        $missing = $query->get();

        // Delete each missing record
        $missing->each->delete();

        // Return the deleted count
        return $missing->count();
    }

    /**
     * Creates and returns the query for deleting records.
     */
    public static function query(string $resource): Builder
    {
        // Create a query identifying the tracking data
        $found = static::$repository->pull($resource);

        // Create a new resource model instance for reference
        $instance = new $resource::$model;

        // Return a query identifying the missing records
        return $instance->newQuery()->whereNotIn($instance->getQualifiedKeyName(), $found);
    }

    /**
     * Sets the repository resolver.
     */
    public static function setRepositoryResolver(Closure $resolver): void
    {
        static::$resolver = $resolver;
    }
}
