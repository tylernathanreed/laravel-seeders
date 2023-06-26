<?php

namespace Reedware\LaravelSeeders\Contracts;

use Illuminate\Support\Enumerable;

interface Reader
{
    /**
     * Reads and returns resource arrays from storage.
     */
    public function read(): Enumerable;
}
