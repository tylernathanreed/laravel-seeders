<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Seed;
use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Seeders\GlossarySeeder;

class TraditionalSeederTest extends TestCase
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
            ['name', 'description'],
            ['dog', 'goes woof'],
        ]));

        $this->seed(GlossarySeeder::class);

        $this->assertEquals(['dog' => 'goes woof'], Glossary::pluck('description', 'name')->all());
        $this->assertNotNull(Glossary::first()->created_at);
        $this->assertNotNull(Glossary::first()->updated_at);
    }

    /**
     * Tests single record updates.
     */
    public function test_a_single_record_can_be_updated()
    {
        Glossary::insert([
            'name' => 'dog',
            'description' => 'barks a lot',
        ]);

        $before = Glossary::first()->updated_at;

        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof'],
        ]));

        $this->seed(GlossarySeeder::class);

        $after = Glossary::first()->updated_at;

        $this->assertEquals(['dog' => 'goes woof'], Glossary::pluck('description', 'name')->all());
        $this->assertNotEquals($after, $before);
    }

    /**
     * Tests unchanged record updates.
     */
    public function test_an_unchanged_record_cannot_be_updated()
    {
        Glossary::insert([
            'name' => 'dog',
            'description' => 'barks a lot',
        ]);

        $before = Glossary::first()->updated_at;

        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'barks a lot'],
        ]));

        $this->seed(GlossarySeeder::class);

        $after = Glossary::first()->updated_at;

        $this->assertEquals($after, $before);
    }

    /**
     * Tests single record deletions.
     */
    public function test_missing_records_are_deleted()
    {
        Glossary::insert([
            ['name' => 'dog', 'description' => 'barks a lot'],
            ['name' => 'cat', 'description' => 'meows a lot'],
        ]);

        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof'],
        ]));

        $this->seed(GlossarySeeder::class);

        $this->assertEquals('goes woof', Glossary::where('name', 'dog')->first()->description);
        $this->assertNull(Glossary::where('name', 'cat')->first());
    }

    /**
     * Tests combinations of creating, updating, and deleting.
     */
    public function test_everything_at_once()
    {
        Glossary::insert([
            ['name' => 'dog', 'description' => 'barks a lot'],
            ['name' => 'cat', 'description' => 'meows a lot'],
        ]);

        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof'],
            ['cow', 'goes moo'],
        ]));

        $this->seed(GlossarySeeder::class);

        $this->assertEquals('goes woof', Glossary::where('name', 'dog')->first()->description);
        $this->assertNull(Glossary::where('name', 'cat')->first());
        $this->assertEquals('goes moo', Glossary::where('name', 'cow')->first()->description);
    }
}
