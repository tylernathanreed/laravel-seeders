<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Seeders\IgnoreColumnGlossarySeeder;
use Reedware\LaravelSeeders\Seed;

class IgnoreColumnSeederTest extends TestCase
{
    /**
     * Sets up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Call the parent method
        parent::setUp();

        // Migrate the glossary table
        Glossary::migrate();
    }

    /**
     * Tests single record creation.
     */
    public function test_a_single_record_can_be_seeded()
    {
        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['id', 'name', 'description', 'is_popular'],
            [9999, 'dog', 'goes woof', 1]
        ]));

        $this->seed(IgnoreColumnGlossarySeeder::class);

        $this->assertNotEquals(9999, Glossary::first()->id);
        $this->assertNull(Glossary::first()->is_popular);
    }
}
