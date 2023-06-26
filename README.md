# Laravel Seeders

This package adds the ability to generate and seed from seed data.

[![Laravel Version](https://img.shields.io/badge/Laravel-9.x%2F10.x-blue)](https://laravel.com/)
[![Automated Tests](https://github.com/tylernathanreed/laravel-seeders/actions/workflows/tests.yml/badge.svg)](https://github.com/tylernathanreed/laravel-seeders/actions/workflows/tests.yml)
[![Coding Standards](https://github.com/tylernathanreed/laravel-seeders/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/tylernathanreed/laravel-seeders/actions/workflows/coding-standards.yml)
[![Static Analysis](https://github.com/tylernathanreed/laravel-seeders/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/tylernathanreed/laravel-seeders/actions/workflows/static-analysis.yml)
[![Latest Stable Version](https://poser.pugx.org/reedware/laravel-seeders/v/stable)](https://packagist.org/packages/reedware/laravel-seeders)

## Introduction

Seeders in Laravel are great for seeding fake data, but that's not their only use case. Seeders are also often used for seeding the boilerplate application data used in glossary type tables (e.g. enumerables, drop-down options, etc.), or tables managed by developers (e.g. roles, permissions, etc.). However, when seeding boilerplate data, there can be a number of complications that arise over time. This package aims to provide seeding application data, and solves the following complications that come with it:

* You want to seed an initial data set, and let the application manage the rest
* You want to seed an initial data set, occasional new entries, and let the application manage the rest
* You want the seeders to have full control over a table
* You want to manage a table through seeding, except for a few columns

## Installation

### Composer

This package can be installed using composer:

```
composer require reedware/laravel-seeders
```

### Service Provider

This package uses auto-discovery to register its service provider. If you choose to manually register the provider, here's the full class path:
```php
'providers' => [
    /* ... */
    Reedware\LaravelSeeders\SeederServiceProvider::class
    /* ... */
]

```

### Facade

This package uses auto-discover to register its facade. If you choose to manually register the facade, here's the default binding:
```php
'aliases' => [
    'Seed' => Reedware\LaravelSeeders\Seed::class
]

```

If you wish to avoid using the facade, you can obtain the underlying instance through the IoC Container:
```php
app('db.seed')
// or
app(Reedware\LaravelSeeders\Contracts\Factory::class)

```

## Usage

### Basic Example

To create your first seeder, start by creating a new seeder like so:

```php
<?php

use Reedware\LaravelSeeders\ResourceSeeder;

class PermissionSeeder extends ResourceSeeder
{
    /**
     * The model this resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Permission::class;

    /**
     * The attributes to match when creating or updating records.
     *
     * @var array
     */
    public static $match = ['name'];

    /**
     * The columns to used order the resources in storage.
     *
     * @var array
     */
    public static $orderBy = ['name' => 'asc'];
}

```

In this example, the seeder will generate seed data from the `permissions` table (derived from the model), and order the line items by the `name` attribute. When the seeder is used to seed from the source data, it will match existing permissions by the `name` property, and update them accordingly.

### Allowed Operations

By default, resource seeders will perform the following operations upon seeding:

* Create database records that for seed data records that don't have a corresponding database record
* Update database records that do have corresponding seed data records
* Delete database records that aren't listed in the seed data

In this operation mode, the seed data is considered to be the source of truth for the database table.

However, you can disable any of the operations like so:

```php
<?php

use Reedware\LaravelSeeders\ResourceSeeder;

class MySeeder extends ResourceSeeder
{
    /* ... */

    /**
     * Whether or not new records in storage can be inserted into the database.
     *
     * @var boolean
     */
    public static $allowCreating = true;

    /**
     * Whether or not existing records in storage can be updated within the database.
     *
     * @var boolean
     */
    public static $allowUpdating = true;

    /**
     * Whether or not missing records in storage can be deleted from the database.
     *
     * @var boolean
     */
    public static $allowDeleting = true;
}
```

Here are the effects of disabling each operation:

* When creating is disabled, no new database records will be created, even if the seed data contains unmatched records
* When updating is disabled, existing records won't be updated (new records can still be created if creating is enabled)
* When deleting is disabled, no database records will be deleted, even if no matching seed data record exists

### Soft Deletion

When a seeded model soft deletes, the behavior of the seeder changes in the following ways:

* Soft deleted database records are still included in seed data generation
* The "deleted_at" column is mapped to a "trashed" boolean in the seed data
* Deletions while seeding execute as soft deletions, providing a fresh timestamp at the time of seeding

### Hard Deletion

When a database record is being deleted, it may have foreign key associations that prevent its deletion. Deletion through seeding deletes through the model, rather than a database query, so that you can leverage the `Model::deleting(...)` event hook to remove or unlink any relationships that would prevent its deletion.

### Primary Keys

By default, seeders do not respect or enforce primary keys. If you wish to generate and seed primary keys, you can enable them like so:

```php
<?php

use Reedware\LaravelSeeders\ResourceSeeder;

class MySeeder extends ResourceSeeder
{
    /* ... */

    /**
     * Whether or not to omit the primary key.
     *
     * @var boolean
     */
    public static $omitPrimaryKey = false;
}
```

### Column Mapping

If you want to change the generation ouput and the seeder intake, you can customize the following methods within your seeder:

```php
<?php

use Reedware\LaravelSeeders\ResourceSeeder;

class MySeeder extends ResourceSeeder
{
    /* ... */

    /**
     * Maps the model attributes to stored attributes.
     *
     * @param  array  $attributes
     *
     * @return array
     */
    public static function mapAttributes(array $attributes)
    {
        $attributes = parent::mapAttributes($attributes);

        $parts = explode(' ', $attributes['full_name']);

        $attributes['first_name'] = array_shift($parts);
        $attributes['last_name'] = $parts;

        return $attributes;
    }

    /**
     * Unmaps stored attributes to model attributes.
     *
     * @param  array  $attributes
     *
     * @return array
     */
    public static function unmapAttributes(array $attributes)
    {
        $attributes = parent::unmapAttributes($attributes);

        $attributes['full_name'] = $attributes['first_name'] . ' ' . $attributes['last_name'];
    }
}
```

### Column Omission

If you want to prevent specific columns from being seeded, you can omit them in your resource seeder:

```php
<?php

use Reedware\LaravelSeeders\ResourceSeeder;

class MySeeder extends ResourceSeeder
{
    /* ... */

    /**
     * Additional columns to omit when seeding.
     *
     * @var array
     */
    public static $omit = [
        'pretend'
    ];
}
```

### Environment Specific Behavior

The configuration options for each resource seeder has been shown using a property. If you want to have environment specific behavior, you won't be able to use properties to do that. Luckily, each property has a corresponding method that you can leverage instead:

```php
<?php

use Reedware\LaravelSeeders\ResourceSeeder;

class MySeeder extends ResourceSeeder
{
    /* ... */

    /**
     * Returns whether or not existing records in storage can be updated within the database.
     *
     * @return boolean
     */
    public static function allowUpdating()
    {
        return app()->environment() !== 'production';
    }
}
```

## Commands

To seed using a seeder from this package, you can call the traditional `php artisan db:seed --class="MyDatabaseSeeder` command.

To generate seed data based on table contents, you can call the new `php artisan db:seed:generate --class="MyDatabaseSeeder` command.

## Data Storage

The data you want to seed into your application should be housed within your source code, and therefore maintained under version control. The location and format of the seed data is customizable if you aren't comfortable with the default configuration.

### Root Path

By default, this package places your seed data within a new `~/database/seeders/data` directory. If you wish to customize this location, you can tap into the `Seed` facade:

```php
<?php

use Illuminate\Support\ServiceProvider;
use Seed;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Seed::setRootPath('where/ever/you/want');
    }
}
```

### Model Mapping

This package uses your models as reference to the source tables, as it detected soft deletion, timestamp usage, and other features that are already configured by your models. Your model is also used to name the seed data file, by converting the model based name to plural snake case (e.g. "MyModel" becomes "my-models.csv").

If you wish to override a particular model, you can tap into the `Seed` facade:
```php
Seed::filename(MyModel::class, 'my-filename.csv');

```

Alternatively, if you wish to customize the default convention, you redefine it:
```php
Seed::guessFilenamesUsing(function($class) {
    return $class . '.csv';
});

```

### File Format

This package uses csv files as external storage, but you may prefer another format. You can override the file reader and writer to use whichever format you prefer:

*Custom Reader:*
```php
<?php

use Reedware\LaravelSeeders\Contracts\Reader;

class MyReader implements Reader
{
    //
}

Seed::readUsing(function($filename) {
    return new MyReader($filename);
});

```

*Custom Writer:*
```php
<?php

use Reedware\LaravelSeeders\Contracts\Writer;

class MyWriter implements Writer
{
    //
}

Seed::writeUsing(function($filename) {
    return new MyWriter($filename);
});

```
