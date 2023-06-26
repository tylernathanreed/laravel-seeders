<?php

namespace Reedware\LaravelSeeders\Tests\Models;

use Illuminate\Database\Schema\Blueprint;

class Glossary extends Model
{
    public function migrateColumns(Blueprint $table)
    {
        $table->string('name', 20)->unique();
        $table->string('description');
        $table->boolean('is_popular')->nullable();
    }
}
