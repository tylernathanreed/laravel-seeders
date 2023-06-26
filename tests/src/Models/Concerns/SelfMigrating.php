<?php

namespace Reedware\LaravelSeeders\Tests\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait SelfMigrating
{
    /**
     * Migrates the table for this model.
     *
     * @return void
     */
    public static function migrate()
    {
        (new static)->migrateTable();
    }

    /**
     * Migrates the table for this model.
     *
     * @return void
     */
    public function migrateTable()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {

            $this->migratePrimaryKey($table);
            $this->migrateColumns($table);
            $this->migrateTimestamps($table);
            $this->migrateSoftDeletes($table);
        });
    }

    /**
     * Migrates the remaining table columns.
     *
     *
     * @return void
     */
    public function migrateColumns(Blueprint $table)
    {
        //
    }

    /**
     * Migrates the primary key column.
     *
     *
     * @return void
     */
    protected function migratePrimaryKey(Blueprint $table)
    {
        $table->addColumn($this->getKeySchemaType(), $this->getKeyName(), [
            'autoIncrement' => $this->getIncrementing(),
            'unsigned' => $this->getIncrementing(),
        ]);

        if (! $this->getIncrementing()) {
            $table->primary($this->getKeyName());
        }
    }

    /**
     * Migrates the timestamp columns.
     *
     *
     * @return void
     */
    protected function migrateTimestamps(Blueprint $table)
    {
        // Make sure this model uses timestamps
        if (! $this->usesTimestamps()) {
            return;
        }

        // Migrate the created at timestamp
        if (! is_null($createdAtColumn = $this->getCreatedAtColumn())) {
            $table->timestamp($createdAtColumn)->nullable();
        }

        // Migrate the updated at timestamp
        if (! is_null($updatedAtColumn = $this->getUpdatedAtColumn())) {
            $table->timestamp($updatedAtColumn)->nullable();
        }
    }

    /**
     * Migrates the soft deletes column.
     *
     *
     * @return void
     */
    protected function migrateSoftDeletes(Blueprint $table)
    {
        // Make sure this model soft deletes
        if (! in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            return;
        }

        // Migrate the soft delete timestamp
        $table->softDeletes($this->getDeletedAtColumn());
    }

    /**
     * Returns the primary key schema type.
     *
     * @return string
     */
    public function getKeySchemaType()
    {
        return [
            'int' => 'integer',
            'bigint' => 'bigInteger',
        ][$this->getKeyType()] ?? $this->getKeyType();
    }
}
