<?php

namespace Reedware\LaravelSeeders\Contracts;

use Illuminate\Support\Enumerable;

interface Writer
{
    /**
     * Writes the specified resource arrays into storage.
     *
     * @param  \Illuminate\Support\Enumerable
     *
     * @return void
     */
    public function write(Enumerable $resources);
}
