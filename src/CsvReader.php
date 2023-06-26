<?php

namespace Reedware\LaravelSeeders;

use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use Reedware\LaravelSeeders\Contracts\Reader;

class CsvReader implements Reader
{
    /**
     * The fully qualified name of the file to read.
     *
     * @var string
     */
    protected $filename;

    /**
     * Creates and returns a new csv reader.
     *
     * @return $this
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * Reads and returns resource arrays from storage.
     */
    public function read(): Enumerable
    {
        return LazyCollection::make(function () {

            // Create the file handle
            $handle = fopen($this->filename, 'r');

            // Determine the header
            $header = fgetcsv($handle);

            // Yield each resource array
            while (($data = fgetcsv($handle)) !== false) {
                yield array_combine($header, array_map(function ($value) {
                    return $value === '' ? null : $value;
                }, $data));
            }

            // Close the file
            fclose($handle);
        });
    }
}
