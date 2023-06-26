<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Seeders\NonDeletingGlossarySeeder;
use Reedware\LaravelSeeders\Seed;

class NonDeletingSeederTest extends TestCase
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
     * Tests single record deletions.
     */
    public function test_missing_records_are_not_deleted()
    {
        Glossary::insert([
            ['name' => 'dog', 'description' => 'barks a lot'],
            ['name' => 'cat', 'description' => 'meows a lot']
        ]);

        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof']
        ]));

        $this->seed(NonDeletingGlossarySeeder::class);

        $this->assertEquals('goes woof', Glossary::where('name', 'dog')->first()->description);
        $this->assertNotNull(Glossary::where('name', 'cat')->first());
    }

    /**
     * Tests combinations of creating, updating, and deleting.
     */
    public function test_everything_at_once()
    {
        Glossary::insert([
            ['name' => 'dog', 'description' => 'barks a lot'],
            ['name' => 'cat', 'description' => 'meows a lot']
        ]);

        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof'],
            ['cow', 'goes moo']
        ]));

        $this->seed(NonDeletingGlossarySeeder::class);

        $this->assertEquals('goes woof', Glossary::where('name', 'dog')->first()->description);
        $this->assertNotNull(Glossary::where('name', 'cat')->first());
        $this->assertEquals('goes moo', Glossary::where('name', 'cow')->first()->description);
    }
}
