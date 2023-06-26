<?php

namespace Reedware\LaravelSeeders\Tests\Seeders;

use Reedware\LaravelSeeders\ResourceSeeder;

class GlossarySeeder extends ResourceSeeder
{
    /**
     * The model this resource corresponds to.
     *
     * @var string
     */
    public static $model = \Reedware\LaravelSeeders\Tests\Models\Glossary::class;

    /**
     * The attributes to match when creating or updating records.
     *
     * @var array
     */
    public static $match = ['name'];

    /**
     * The columns to used order the resources in storage.
     *
     * @var array
     */
    public static $orderBy = ['name' => 'asc'];
}
