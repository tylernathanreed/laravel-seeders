<?php

namespace Reedware\LaravelSeeders\Tests;

use Illuminate\Support\Arr;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Reedware\LaravelSeeders\Seed;

abstract class TestCase extends BaseTestCase
{
    use Concerns\DefinesIntegration,
        Concerns\ManagesDataFiles;

    /**
     * Prepares the testing environment before the current test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Seed::setRootPath(realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'data'
        ])));
    }

    /**
     * Cleans up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->clearDataFiles();
    }

    /**
     * Generates seed data from the specified seeder(s).
     *
     * @param  array|string  $class
     * *
     * @return $this
     */
    public function generate($class = 'DatabaseSeeder')
    {
        foreach (Arr::wrap($class) as $class) {
            $this->artisan('db:seed:generate', ['--class' => $class, '--no-interaction' => true]);
        }

        return $this;
    }
}
