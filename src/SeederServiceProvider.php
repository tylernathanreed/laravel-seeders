<?php

namespace Reedware\LaravelSeeders;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SeederServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFactory();
        $this->registerResourceRepository();
    }

    /**
     * Registers the factory implementation.
     *
     * @return void
     */
    protected function registerFactory()
    {
        $this->app->singleton(Contracts\Factory::class, function ($app) {
            return new Factory($app->databasePath('seeders'.DIRECTORY_SEPARATOR.'data'));
        });

        $this->app->alias(Contracts\Factory::class, 'db.seed');
    }

    /**
     * Registers the resource repository.
     *
     * @return void
     */
    protected function registerResourceRepository()
    {
        $this->app->singleton(ResourceRepository::class, function ($app) {
            return new ResourceRepository($app['cache.store']);
        });
    }

    /**
     * Bootstraps the package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootCommands();

        DeleteMissing::setRepositoryResolver(function () {
            return $this->app->make(ResourceRepository::class);
        });
    }

    /**
     * Boots the console commands.
     *
     * @return void
     */
    protected function bootCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Commands\GenerateCommand::class,
        ]);
    }

    /**
     * Returns the services provided by this provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Contracts\Factory::class,
        ];
    }
}
