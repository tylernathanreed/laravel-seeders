<?php

namespace Reedware\LaravelSeeders\Concerns;

use Closure;
use Reedware\LaravelSeeders\CsvWriter;
use Reedware\LaravelSeeders\Contracts\Writer;

trait CreatesWriters
{
    /**
     * The custom writer resolver callback.
     *
     * @var \Closure|null
     */
    protected $writerResolver;

    /**
     * Returns a new writer for the specified resource.
     *
     * @param  string  $resource
     *
     * @return \Reedware\LaravelSeeders\Contracts\Writer
     */
    public function newWriterFor(string $resource): Writer
    {
        // Determine the filename
        $filename = $this->getFilenameFor($resource);

        // Create and return a new writer
        return $this->createWriter($filename);
    }

    /**
     * Creates and returns a new writer for the specified filename.
     *
     * @param  string  $filename
     *
     * @return \Reedware\LaravelSeeders\Contracts\Writer
     */
    protected function createWriter(string $filename)
    {
        // If a custom resolver exists, use it
        if (!is_null($this->writerResolver)) {
            return call_user_func($this->writerResolver, $filename);
        }

        // Otherwise, use a csv writer
        return new CsvWriter($filename);
    }

    /**
     * Sets the writer resolver callback.
     *
     * @param  \Closure  $resolver
     *
     * @return $this
     */
    public function writeUsing(Closure $resolver)
    {
        $this->writerResolver = $resolver;

        return $this;
    }
}
