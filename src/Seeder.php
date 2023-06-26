<?php

namespace Reedware\LaravelSeeders;

use Illuminate\Database\Seeder as BaseSeeder;
use Illuminate\Support\Arr;
use Reedware\LaravelSeeders\Contracts\Factory as FactoryContract;
use RuntimeException;

abstract class Seeder extends BaseSeeder
{
    /**
     * Seed the given connection from the given path.
     *
     * @param  array|string  $class
     * @param  bool  $silent
     * @return $this
     */
    public function call($class, $silent = false, array $parameters = [])
    {
        $classes = Arr::wrap($class);

        foreach ($classes as $class) {
            $seeder = $this->resolve($class);

            $name = get_class($seeder);

            if ($silent === false && isset($this->command)) {
                $this->command->getOutput()->writeln("<comment>Seeding:</comment> {$name}");
            }

            $startTime = microtime(true);

            [$creates, $updates, $deletes] = $seeder->__invoke($parameters);

            $runTime = round(microtime(true) - $startTime, 2);

            if ($silent === false && isset($this->command)) {
                $this->command->getOutput()->writeln(
                    sprintf(
                        '<info>Seeded:</info>  %s (%s seconds) [%s Insert(s), %s Update(s), %s Delete(s)]',
                        $name,
                        $runTime,
                        $creates,
                        $updates,
                        $deletes
                    )
                );
            }
        }

        return $this;
    }

    /**
     * Runs this seeder.
     *
     * @param  \Reedware\LaravelSeeders\Contracts\Factory  $factory
     *
     * @return void
     */
    public function run(FactoryContract $factory)
    {
        return $this->fromResourceStorage($factory);
    }

    /**
     * Reads the records from storage into the resource table.
     *
     * @param  \Reedware\LaravelSeeders\Contracts\Factory  $factory
     *
     * @return array
     */
    protected function fromResourceStorage(FactoryContract $factory)
    {
        // Determine the resource
        $resource = $this->resource();

        // Create a new reader
        $reader = $factory->newReaderFor($resource);

        // Read and hydrate the records from storage
        return $resource::fromStorage($reader->read());
    }

    /**
     * Generates the seed data from the resource table.
     *
     * @param  \Reedware\LaravelSeeders\Contracts\Factory  $factory
     *
     * @return void
     */
    public function generate(FactoryContract $factory)
    {
        $this->toResourceStorage($factory);
    }

    /**
     * Reads the records from storage into the resource table.
     *
     * @param  \Reedware\LaravelSeeders\Contracts\Factory  $factory
     *
     * @return void
     */
    protected function toResourceStorage(FactoryContract $factory)
    {
        // Determine the resource
        $resource = $this->resource();

        // Create a new writer
        $writer = $factory->newWriterFor($resource);

        // Collect and write the records to storage
        $writer->write($resource::toStorage());
    }

    /**
     * Returns the associated resource class.
     *
     * @return string
     */
    public function resource()
    {
        throw new RuntimeException('Seeder [' . static::class . '] has no associated resource.');
    }
}
