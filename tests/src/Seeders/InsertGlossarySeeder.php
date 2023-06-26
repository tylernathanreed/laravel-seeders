<?php

namespace Reedware\LaravelSeeders\Tests\Seeders;

class InsertGlossarySeeder extends GlossarySeeder
{
    /**
     * The model this resource corresponds to.
     *
     * @var string
     */
    public static $model = \Reedware\LaravelSeeders\Tests\Models\InsertGlossary::class;

    /**
     * Whether or not existing records in storage can be updated within the database.
     *
     * @var bool
     */
    public static $allowUpdating = false;

    /**
     * Whether or not missing records in storage can be deleted from the database.
     *
     * @var bool
     */
    public static $allowDeleting = false;
}
