<?php

namespace Reedware\LaravelSeeders;

use Reedware\LaravelSeeders\Contracts\Factory as FactoryContract;

class Factory implements FactoryContract
{
    use Concerns\CreatesReaders,
        Concerns\CreatesWriters,
        Concerns\ResolvesFilenames;

    /**
     * Creates a new factory instance.
     *
     * @param  string  $rootPath
     * @return $this
     */
    public function __construct($rootPath)
    {
        $this->rootPath = $rootPath;
    }
}
