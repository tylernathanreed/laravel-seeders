<?php

namespace Reedware\LaravelSeeders\Tests\Concerns;

use Reedware\LaravelSeeders\SeederServiceProvider;

trait DefinesIntegration
{
    /**
     * Defines the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $this->defineDatabaseEnvironment($app);
    }

    /**
     * Defines the database configuration for the application.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineDatabaseEnvironment($app)
    {
        // Override the default connection
        $app['config']->set('database.default', 'test');

        // Define the default connection
        $app['config']->set('database.connections.test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Returns the package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SeederServiceProvider::class,
        ];
    }
}
