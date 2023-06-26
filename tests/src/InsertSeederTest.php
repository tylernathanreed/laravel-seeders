<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Tests\Models\InsertGlossary;
use Reedware\LaravelSeeders\Tests\Seeders\InsertGlossarySeeder;
use Reedware\LaravelSeeders\Seed;

class InsertSeederTest extends TestCase
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
        InsertGlossary::migrate();
    }

    /**
     * Tests single record creation.
     */
    public function test_a_single_record_can_be_seeded()
    {
        Seed::filename(InsertGlossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof']
        ]));

        $this->seed(InsertGlossarySeeder::class);

        $this->assertEquals(['dog' => 'goes woof'], InsertGlossary::pluck('description', 'name')->all());
        $this->assertNotNull(InsertGlossary::first()->created_at);
        $this->assertNull(InsertGlossary::first()->updated_at);
    }

    /**
     * Tests single record updates.
     */
    public function test_a_single_record_cannot_be_updated()
    {
        InsertGlossary::insert([
            'name' => 'dog',
            'description' => 'barks a lot'
        ]);

        $before = InsertGlossary::first()->updated_at;

        Seed::filename(InsertGlossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof']
        ]));

        $this->seed(InsertGlossarySeeder::class);

        $after = InsertGlossary::first()->updated_at;

        $this->assertEquals(['dog' => 'barks a lot'], InsertGlossary::pluck('description', 'name')->all());
        $this->assertEquals($after, $before);
    }
}
