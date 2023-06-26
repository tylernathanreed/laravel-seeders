<?php

namespace Reedware\LaravelSeeders\Tests;

use Illuminate\Support\Facades\Date;
use Reedware\LaravelSeeders\Seed;
use Reedware\LaravelSeeders\Tests\Models\DeletableGlossary;
use Reedware\LaravelSeeders\Tests\Seeders\DeletableGlossarySeeder;

class DeletableSeederTest extends TestCase
{
    /**
     * Sets up the test environment.
     */
    protected function setUp(): void
    {
        // Call the parent method
        parent::setUp();

        // Migrate the glossary table
        DeletableGlossary::migrate();
    }

    /**
     * Tests soft deletes detection.
     */
    public function test_soft_deletes_detection()
    {
        $this->assertTrue(DeletableGlossarySeeder::softDeletes());
    }

    /**
     * Tests single record soft deletes.
     */
    public function test_a_single_record_can_be_soft_deleted()
    {
        DeletableGlossary::insert([
            'name' => 'dog',
            'description' => 'barks a lot',
        ]);

        Seed::filename(DeletableGlossary::class, $this->writeCsv('data.csv', [
            ['name', 'description', 'trashed'],
            ['dog', 'goes woof', '1'],
        ]));

        $this->seed(DeletableGlossarySeeder::class);

        $this->assertNull(DeletableGlossary::first());
        $this->assertNotNull(DeletableGlossary::withTrashed()->first());
    }

    /**
     * Tests already-deleted record soft deletes.
     */
    public function test_a_deleted_record_cannot_be_soft_deleted()
    {
        $before = (new DeletableGlossary)->fromDateTime(Date::parse('-1 day'));

        DeletableGlossary::insert([
            'name' => 'dog',
            'description' => 'barks a lot',
            'deleted_at' => $before,
        ]);

        Seed::filename(DeletableGlossary::class, $this->writeCsv('data.csv', [
            ['name', 'description', 'trashed'],
            ['dog', 'barks a lot', '1'],
        ]));

        $this->seed(DeletableGlossarySeeder::class);

        $after = DeletableGlossary::withTrashed()->first()->deleted_at;

        $this->assertEquals($before, $after);
    }

    /**
     * Tests single record restores.
     */
    public function test_a_single_record_can_be_restored()
    {
        DeletableGlossary::insert([
            'name' => 'dog',
            'description' => 'barks a lot',
            'deleted_at' => (new DeletableGlossary)->freshTimestamp(),
        ]);

        Seed::filename(DeletableGlossary::class, $this->writeCsv('data.csv', [
            ['name', 'description', 'trashed'],
            ['dog', 'goes woof', '0'],
        ]));

        $this->seed(DeletableGlossarySeeder::class);

        $this->assertNotNull(DeletableGlossary::first());
        $this->assertNull(DeletableGlossary::onlyTrashed()->first());
    }

    /**
     * Tests missing soft deletes.
     */
    public function test_missing_records_are_soft_deleted()
    {
        DeletableGlossary::insert([
            ['name' => 'dog', 'description' => 'barks a lot'],
            ['name' => 'cat', 'description' => 'meows a lot'],
        ]);

        Seed::filename(DeletableGlossary::class, $this->writeCsv('data.csv', [
            ['name', 'description', 'trashed'],
            ['dog', 'goes woof', '0'],
        ]));

        $this->seed(DeletableGlossarySeeder::class);

        $this->assertNotNull(DeletableGlossary::where('name', 'dog')->first());
        $this->assertNull(DeletableGlossary::where('name', 'cat')->first());
        $this->assertNotNull(DeletableGlossary::withTrashed()->where('name', 'cat')->first());
    }
}
