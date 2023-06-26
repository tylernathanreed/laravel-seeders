<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Seeders\ExistingGlossarySeeder;
use Reedware\LaravelSeeders\Seed;

class ExistingSeederTest extends TestCase
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
    public function test_a_single_record_cannot_be_seeded()
    {
        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof']
        ]));

        $this->seed(ExistingGlossarySeeder::class);

        $this->assertNull(Glossary::first());
    }

    /**
     * Tests single record updates.
     */
    public function test_a_single_record_can_be_updated()
    {
        Glossary::insert([
            'name' => 'dog',
            'description' => 'barks a lot'
        ]);

        $before = Glossary::first()->updated_at;

        Seed::filename(Glossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof']
        ]));

        $this->seed(ExistingGlossarySeeder::class);

        $after = Glossary::first()->updated_at;

        $this->assertEquals(['dog' => 'goes woof'], Glossary::pluck('description', 'name')->all());
        $this->assertNotEquals($after, $before);
    }
}
