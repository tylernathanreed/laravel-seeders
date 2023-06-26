<?php

namespace Reedware\LaravelSeeders\Tests\Concerns;

use Closure;
use Reedware\LaravelSeeders\Seed;

trait ManagesDataFiles
{
    /**
     * The list of data files being managed.
     *
     * @var array
     */
    protected $dataFiles = [];

    /**
     * Writes the specified data to the given csv file.
     *
     * @param  string  $filename
     * @param  array   $data
     *
     * @return string
     */
    protected function writeCsv(string $filename, array $data)
    {
        return $this->writeData($filename, function ($file) use ($data) {
            foreach ($data as $fields) {
                fputcsv($file, $fields);
            }
        });
    }

    /**
     * Writes to the specified data file.
     *
     * @param  string    $filename
     * @param  \Closure  $handler
     *
     * @return string
     */
    protected function writeData(string $filename, Closure $handler)
    {
        // Determine the full file path
        $filepath = Seed::rootPath($filename);

        // Open the file
        $file = fopen($filepath, 'w');

        // Write to the file
        $handler($file);

        // Close the file
        fclose($file);

        // Mark the file for clean up
        $this->dataFiles[] = $filepath;

        // Return the local filename
        return $filename;
    }

    /**
     * Removes all data files.
     *
     * @return void
     */
    protected function clearDataFiles()
    {
        foreach ($this->dataFiles as $filepath) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        $this->dataFiles = [];
    }
}
