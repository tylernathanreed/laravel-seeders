<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Seed;
use Reedware\LaravelSeeders\Tests\Models\DeletableGlossary;
use Reedware\LaravelSeeders\Tests\Seeders\DeletableGlossarySeeder;

class DeletableGeneratorTest extends TestCase
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

        // Delete the data csv after each test
        $this->dataFiles[] = Seed::rootPath('data.csv');
    }

    /**
     * Tests single record generation.
     */
    public function test_deleted_at_persists_as_trashed()
    {
        DeletableGlossary::insert([
            ['name' => 'dog', 'description' => 'goes woof', 'deleted_at' => null],
            ['name' => 'cat', 'description' => 'goes meow', 'deleted_at' => (new DeletableGlossary)->freshTimestamp()],
        ]);

        Seed::filename(DeletableGlossary::class, 'data.csv');

        $this->generate(DeletableGlossarySeeder::class);

        $this->assertTrue(file_exists(Seed::rootPath('data.csv')));

        $this->assertEquals("name,description,is_popular,trashed\ncat,\"goes meow\",,1\ndog,\"goes woof\",,\n", file_get_contents(Seed::rootPath('data.csv')));
    }
}
