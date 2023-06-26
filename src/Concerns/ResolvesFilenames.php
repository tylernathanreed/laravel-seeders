<?php

namespace Reedware\LaravelSeeders\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait ResolvesFilenames
{
    /**
     * All of the defined filenames.
     *
     * @var array
     */
    protected $filenames = [];

    /**
     * The user-defined callback for guessing filenames.
     *
     * @var callable|null
     */
    protected $guessFilenamesUsingCallback;

    /**
     * The custom root path for filenames.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * Defines the filename to use with the specified resource or model class.
     *
     * @param  string  $class
     * @param  string  $filename
     *
     * @return $this
     */
    public function filename(string $class, string $filename)
    {
        $this->filenames[$class] = $filename;

        if (isset($class::$model) && !isset($this->filenames[$class::$model])) {
            $this->filenames[$class::$model] = $filename;
        }

        return $this;
    }

    /**
     * Returns the filename for the specified resource class.
     *
     * @param  object|string  $class
     *
     * @return string|null
     */
    public function getFilenameFor($class)
    {
        // Convert objects to class names
        if (is_object($class)) {
            $class = get_class($class);
        }

        // Make sure the class is a string
        if (!is_string($class)) {
            return null;
        }

        // If the filename was explicitly set, use it
        if (isset($this->filenames[$class])) {
            return $this->rootPath($this->filenames[$class]);
        }

        // Otherwise, try to guess the filename name instead
        foreach ($this->guessFilename($class) as $guessedFilename) {
            if (file_exists($this->rootPath($guessedFilename))) {
                return $this->rootPath($guessedFilename);
            }
        }

        // If the class is a resource, try again using its model
        if (isset($class::$model)) {
            return $this->getFilenameFor($class::$model);
        }

        // Unknown filename
        return null;
    }

    /**
     * Returns a list of guessed filenames to try based on the specified resource class.
     *
     * @param  string  $class
     *
     * @return array
     */
    protected function guessFilename(string $class)
    {
        // If a custom guesser exists, use it
        if (!is_null($this->guessFilenamesUsingCallback)) {
            return Arr::wrap(call_user_func($this->guessFilenamesUsingCallback, $class));
        }

        // Otherwise, use the default guesser
        return $this->guessFilenameUsingBasename($class);
    }

    /**
     * Returns the guessed filename to try based on the basename of the specified class.
     *
     * @param  string  $class
     *
     * @return array
     */
    public function guessFilenameUsingBasename(string $class)
    {
        return [Str::snake(Str::plural(class_basename($class)), '_') . '.csv'];
    }

    /**
     * Sets the callback to be used to guess filenames.
     *
     * @param  callable  $callback
     *
     * @return $this
     */
    public function guessFilenamesUsing(callable $callback)
    {
        $this->guessFilenamesUsingCallback = $callback;

        return $this;
    }

    /**
     * Returns the root path for filenames.
     *
     * @param  string  $path
     *
     * @return string
     */
    public function rootPath($path = '')
    {
        return $this->rootPath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Sets the root path for filenames.
     *
     * @param  string  $path
     *
     * @return $this
     */
    public function setRootPath($path)
    {
        $this->rootPath = $path;

        return $this;
    }
}
