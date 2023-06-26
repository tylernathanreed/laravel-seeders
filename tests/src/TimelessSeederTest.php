<?php

namespace Reedware\LaravelSeeders\Tests;

use Illuminate\Support\Facades\Schema;
use Reedware\LaravelSeeders\Tests\Models\TimelessGlossary;
use Reedware\LaravelSeeders\Tests\Seeders\TimelessGlossarySeeder;
use Reedware\LaravelSeeders\Seed;

class TimelessSeederTest extends TestCase
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
        TimelessGlossary::migrate();
    }

    /**
     * Tests single record creation.
     */
    public function test_a_single_record_can_be_seeded()
    {
        Seed::filename(TimelessGlossary::class, $this->writeCsv('data.csv', [
            ['name', 'description'],
            ['dog', 'goes woof']
        ]));

        $this->seed(TimelessGlossarySeeder::class);

        $this->assertEquals(['dog' => 'goes woof'], TimelessGlossary::pluck('description', 'name')->all());
        $this->assertNull(TimelessGlossary::first()->created_at);
        $this->assertNull(TimelessGlossary::first()->updated_at);
    }
}
