<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Seed;
use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Seeders\GlossarySeeder;

class FilenameResolutionTest extends TestCase
{
    public function test_empty_binding()
    {
        $this->assertEquals(null, Seed::getFilenameFor(Glossary::class));
    }

    public function test_resource_binding()
    {
        Seed::filename(GlossarySeeder::class, 'example');

        $this->assertEquals(Seed::rootPath('example'), Seed::getFilenameFor(GlossarySeeder::class));
    }

    public function test_implicit_model_binding()
    {
        Seed::filename(GlossarySeeder::class, 'example');

        $this->assertEquals(Seed::rootPath('example'), Seed::getFilenameFor(Glossary::class));
    }

    public function test_explicit_model_binding()
    {
        Seed::filename(Glossary::class, 'example');

        $this->assertEquals(Seed::rootPath('example'), Seed::getFilenameFor(Glossary::class));
    }

    public function test_inverse_model_binding()
    {
        Seed::filename(Glossary::class, 'example');

        $this->assertEquals(Seed::rootPath('example'), Seed::getFilenameFor(GlossarySeeder::class));
    }

    public function test_guess_binding()
    {
        $real = Seed::rootPath('example.csv');
        touch($real);

        Seed::guessFilenamesUsing(function ($class) {
            return ['not a real file', 'example.csv'];
        });

        $this->assertEquals($real, Seed::getFilenameFor(Glossary::class));

        unlink($real);
    }
}
