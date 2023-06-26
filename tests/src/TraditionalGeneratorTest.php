<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Seed;
use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Seeders\GlossarySeeder;

class TraditionalGeneratorTest extends TestCase
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

        // Delete the data csv after each test
        $this->dataFiles[] = Seed::rootPath('data.csv');
    }

    /**
     * Tests single record generation.
     */
    public function test_a_single_record_can_be_generated()
    {
        Glossary::insert([
            'name' => 'dog',
            'description' => 'goes woof',
        ]);

        Seed::filename(Glossary::class, 'data.csv');

        $this->generate(GlossarySeeder::class);

        $this->assertTrue(file_exists(Seed::rootPath('data.csv')));

        $this->assertEquals("name,description,is_popular\ndog,\"goes woof\",\n", file_get_contents(Seed::rootPath('data.csv')));
    }
}
