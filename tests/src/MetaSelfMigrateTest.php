<?php

namespace Reedware\LaravelSeeders\Tests;

use Illuminate\Support\Facades\Schema;
use Reedware\LaravelSeeders\Tests\Models\DeletableGlossary;
use Reedware\LaravelSeeders\Tests\Models\Glossary;
use Reedware\LaravelSeeders\Tests\Models\HashableGlossary;
use Reedware\LaravelSeeders\Tests\Models\TimelessGlossary;
use Reedware\LaravelSeeders\Tests\Models\InsertGlossary;

class MetaSelfMigrateTest extends TestCase
{
    public function test_traditional_self_migrate()
    {
        Glossary::migrate();

        $this->assertTrue(Schema::hasTable('glossaries'));
        $this->assertTrue(Schema::hasColumn('glossaries', 'id'));
        $this->assertTrue(Schema::hasColumn('glossaries', 'name'));
        $this->assertTrue(Schema::hasColumn('glossaries', 'description'));
        $this->assertTrue(Schema::hasColumn('glossaries', 'created_at'));
        $this->assertTrue(Schema::hasColumn('glossaries', 'updated_at'));
        $this->assertFalse(Schema::hasColumn('glossaries', 'deleted_at'));
    }

    public function test_soft_delete_self_migrate()
    {
        DeletableGlossary::migrate();

        $this->assertTrue(Schema::hasTable('deletable_glossaries'));
        $this->assertTrue(Schema::hasColumn('deletable_glossaries', 'id'));
        $this->assertTrue(Schema::hasColumn('deletable_glossaries', 'name'));
        $this->assertTrue(Schema::hasColumn('deletable_glossaries', 'description'));
        $this->assertTrue(Schema::hasColumn('deletable_glossaries', 'created_at'));
        $this->assertTrue(Schema::hasColumn('deletable_glossaries', 'updated_at'));
        $this->assertTrue(Schema::hasColumn('deletable_glossaries', 'deleted_at'));
    }

    public function test_non_incrementing_primary_key_self_migrate()
    {
        HashableGlossary::migrate();

        $this->assertTrue(Schema::hasTable('hashable_glossaries'));
        $this->assertTrue(Schema::hasColumn('hashable_glossaries', 'uuid'));
        $this->assertTrue(Schema::hasColumn('hashable_glossaries', 'name'));
        $this->assertTrue(Schema::hasColumn('hashable_glossaries', 'description'));
        $this->assertTrue(Schema::hasColumn('hashable_glossaries', 'created_at'));
        $this->assertTrue(Schema::hasColumn('hashable_glossaries', 'updated_at'));
        $this->assertFalse(Schema::hasColumn('hashable_glossaries', 'deleted_at'));
    }

    public function test_timeless_self_migrate()
    {
        TimelessGlossary::migrate();

        $this->assertTrue(Schema::hasTable('timeless_glossaries'));
        $this->assertTrue(Schema::hasColumn('timeless_glossaries', 'id'));
        $this->assertTrue(Schema::hasColumn('timeless_glossaries', 'name'));
        $this->assertTrue(Schema::hasColumn('timeless_glossaries', 'description'));
        $this->assertFalse(Schema::hasColumn('timeless_glossaries', 'created_at'));
        $this->assertFalse(Schema::hasColumn('timeless_glossaries', 'updated_at'));
        $this->assertFalse(Schema::hasColumn('timeless_glossaries', 'deleted_at'));
    }

    public function test_insert_self_migrate()
    {
        InsertGlossary::migrate();

        $this->assertTrue(Schema::hasTable('insert_glossaries'));
        $this->assertTrue(Schema::hasColumn('insert_glossaries', 'id'));
        $this->assertTrue(Schema::hasColumn('insert_glossaries', 'name'));
        $this->assertTrue(Schema::hasColumn('insert_glossaries', 'description'));
        $this->assertTrue(Schema::hasColumn('insert_glossaries', 'created_at'));
        $this->assertFalse(Schema::hasColumn('insert_glossaries', 'updated_at'));
        $this->assertFalse(Schema::hasColumn('insert_glossaries', 'deleted_at'));
    }
}
