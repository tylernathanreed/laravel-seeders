<?php

namespace Reedware\LaravelSeeders\Tests\Seeders;

class IgnoreColumnGlossarySeeder extends GlossarySeeder
{
    /**
     * Additional columns to omit when seeding.
     *
     * @var array
     */
    public static $omit = [
        'is_popular'
    ];
}
