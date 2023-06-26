<?php

namespace Reedware\LaravelSeeders\Tests;

use Reedware\LaravelSeeders\Seed;

class MetaDataFilesTest extends TestCase
{
    public function test_csv_write()
    {
        $filename = $this->writeCsv('example.csv', [
            ['one', 'two'],
            ['three', 'four'],
        ]);

        $filepath = Seed::rootPath($filename);

        $this->assertTrue(file_exists($filepath));

        $this->assertEquals("one,two\nthree,four\n", file_get_contents($filepath));

        $this->clearDataFiles();

        $this->assertFalse(file_exists($filepath));
    }
}
