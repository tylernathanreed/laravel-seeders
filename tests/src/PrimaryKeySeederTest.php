<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Seed;
use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Seeders\PrimaryKeyGlossarySeeder;

class PrimaryKeySeederTest extends TestCase
{
    /**
     * Sets up the test environment.
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
            ['id', 'name', 'description'],
            [9999, 'dog', 'goes woof'],
        ]));

        $this->seed(PrimaryKeyGlossarySeeder::class);

        $this->assertEquals(9999, Glossary::first()->id);
    }
}
