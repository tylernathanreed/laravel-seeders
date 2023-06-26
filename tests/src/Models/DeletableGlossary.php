<?php

namespace Reedware\LaravelSeeders\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DeletableGlossary extends Glossary
{
    use SoftDeletes;
}
