<?php

namespace Reedware\LaravelSeeders;

abstract class ResourceSeeder extends Seeder
{
    use Concerns\BehavesAsResource;

    /**
     * Returns the associated resource class.
     *
     * @return string
     */
    public function resource()
    {
        return static::class;
    }
}
