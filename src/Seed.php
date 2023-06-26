<?php

namespace Reedware\LaravelSeeders;

use Illuminate\Support\Facades\Facade;

class Seed extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Contracts\Factory::class;
    }
}
