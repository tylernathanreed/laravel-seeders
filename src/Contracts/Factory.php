<?php

namespace Reedware\LaravelSeeders\Contracts;

interface Factory
{
    /**
     * Creates and returns a new reader for the specified resource class.
     *
     * @return \Reedware\LaravelSeeders\Contracts\Reader
     */
    public function newReaderFor(string $resource): Reader;

    /**
     * Creates and returns a new writer for the specified resource class.
     *
     * @return \Reedware\LaravelSeeders\Contracts\Writer
     */
    public function newWriterFor(string $resource): Writer;

    /**
     * Defines the filename to use with the specified resource or model class.
     *
     * @return $this
     */
    public function filename(string $class, string $filename);

    /**
     * Returns the filename for the specified resource class.
     *
     * @param  object|string  $class
     * @return string|null
     */
    public function getFilenameFor($class);
}
