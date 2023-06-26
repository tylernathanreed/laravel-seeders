<?php

namespace Reedware\LaravelSeeders\Tests\Seeders;

class ExistingGlossarySeeder extends GlossarySeeder
{
    /**
     * Whether or not new records in storage can be inserted into the database.
     *
     * @var boolean
     */
    public static $allowCreating = false;

    /**
     * Whether or not missing records in storage can be deleted from the database.
     *
     * @var boolean
     */
    public static $allowDeleting = false;
}
