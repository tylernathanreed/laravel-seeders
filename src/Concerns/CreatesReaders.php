<?php

namespace Reedware\LaravelSeeders\Concerns;

use Closure;
use Reedware\LaravelSeeders\CsvReader;
use Reedware\LaravelSeeders\Contracts\Reader;

trait CreatesReaders
{
    /**
     * The custom reader resolver callback.
     *
     * @var \Closure|null
     */
    protected $readerResolver;

    /**
     * Returns a new reader for the specified resource.
     *
     * @param  string  $resource
     *
     * @return \Reedware\LaravelSeeders\Contracts\Reader
     */
    public function newReaderFor(string $resource): Reader
    {
        // Determine the filename
        $filename = $this->getFilenameFor($resource);

        // Create and return a new reader
        return $this->createReader($filename);
    }

    /**
     * Creates and returns a new reader for the specified filename.
     *
     * @param  string  $filename
     *
     * @return \Reedware\LaravelSeeders\Contracts\Reader
     */
    protected function createReader(string $filename)
    {
        // If a custom resolver exists, use it
        if (!is_null($this->readerResolver)) {
            return call_user_func($this->readerResolver, $filename);
        }

        // Otherwise, use a csv reader
        return new CsvReader($filename);
    }

    /**
     * Sets the reader resolver callback.
     *
     * @param  \Closure  $resolver
     *
     * @return $this
     */
    public function readUsing(Closure $resolver)
    {
        $this->readerResolver = $resolver;

        return $this;
    }
}
