<?php

namespace Reedware\LaravelSeeders\Commands;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Reedware\LaravelSeeders\Contracts\Factory as FactoryContract;

class GenerateCommand extends SeedCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:seed:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seed data from the database';

    /**
     * The seeder factory implementation.
     *
     * @var \Reedware\LaravelSeeders\Contracts\Factory
     */
    protected $factory;

    /**
     * Create a new database seed command instance.
     *
     *
     * @return void
     */
    public function __construct(FactoryContract $factory, Resolver $resolver)
    {
        parent::__construct($resolver);

        $this->factory = $factory;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $previousConnection = $this->resolver->getDefaultConnection();

        $this->resolver->setDefaultConnection($this->getDatabase());

        $seeder = $this->getSeeder();

        $filename = $this->factory->getFilenameFor($seeder->resource())
            ?: $this->factory->rootPath($this->factory->guessFilenameUsingBasename($seeder->resource()::$model)[0]);

        touch($filename);

        $this->laravel->call([$seeder, 'generate']);

        if ($previousConnection) {
            $this->resolver->setDefaultConnection($previousConnection);
        }

        $this->info('Database seeding completed successfully.');

        return 0;
    }
}
