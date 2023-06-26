<?php

namespace Reedware\LaravelSeeders\Tests\Seeders;

class NonDeletingGlossarySeeder extends GlossarySeeder
{
    /**
     * Whether or not missing records in storage can be deleted from the database.
     *
     * @var bool
     */
    public static $allowDeleting = false;
}
