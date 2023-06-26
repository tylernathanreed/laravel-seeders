<?php

namespace Reedware\LaravelSeeders;

use Illuminate\Support\Enumerable;
use Reedware\LaravelSeeders\Contracts\Writer;

class CsvWriter implements Writer
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
     *
     * @return void
     */
    public function write(Enumerable $records)
    {
        // Create the file handle
        $handle = fopen($this->filename, 'w');

        // Initialize the header flag
        $header = false;

        // Write each record
        $records->each(function ($record) use ($handle, &$header) {

            // Check if the header hasn't been written
            if (! $header) {
                // Write the header
                fputcsv($handle, array_keys($record));

                // Don't write the header again
                $header = true;
            }

            // Write the record
            fputcsv($handle, $record);
        });

        // Close the file
        fclose($handle);
    }
}
