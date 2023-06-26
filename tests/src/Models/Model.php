<?php

namespace Reedware\LaravelSeeders\Tests\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent
{
    use Concerns\SelfMigrating;
}
