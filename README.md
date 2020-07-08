# Laravel Model Generator

![License](https://img.shields.io/packagist/l/corex/lmodel.svg)
![Build Status](https://travis-ci.org/corex/lmodel.svg?branch=master)
![codecov](https://codecov.io/gh/corex/lmodel/branch/master/graph/badge.svg)

**Note that from version 1 to version 2 there are breaking changes as the package has been rewritten from scratch.**

Laravel has a really nice approach to new databases, but requires more work if
you have existing databases. Of course, it is possible to manage existing
databases, but these databases are often maintained externally. A model
generator can go a long way to help.

Connect to your existing database and generate models based on existing schema.
- Support for "declare(strict_types=1);".
- Support for multiple connections.
- Support for auto-completion via phpdoc properties.
- Support for custom code (preserved lines).
- Support for fillable fields.
- Support for guarded fields.
- Support for custom "extends".
- Support for building multiple groups of constants in model.
- Support for custom "indent".
- Support for Doctrine type mapping ("enum" mapped to "string", ...).
- Support for PhpDoc type mapping.
- Support for ignored tables.
- Support for showing changes before writing models (--dry-run).

**Note: Generating a model that already exists will overwrite existing model, but every line below "preserve" identifier, will be preserved.**


## Installation
Run ```"composer require corex/lmodel --dev"```.

Copy "{root}/vendor/corex/lmodel/config/lmodel.php" from package to "{root}/config/lmodel.php" and modify it to suit your needs.

To register it and make sure you have this option available for development only, add following code to AppServiceProviders@register method.
```php
if ($this->app->environment('local')) {
    $this->app->register(\CoRex\Laravel\Model\ModelServiceProvider::class);
}
```


## Configuration
Each setting is explained in copied config-file ("{root}/config/lmodel.php").


## Replacing a builder
Following is a list of builders that can be replaced by extending existing
builder, extending BaseBuilder or implementing BuilderInterface. It you plan
to write your own builder, it is strongly recommended to extend BaseBuilder
since it already contains "basic plumbing".

Existing builders (executed in this order).
- DeclareStrictBuilder
- NamespaceBuilder
- UsesBuilder
- PhpDocBuilder
- ClassExtendsBuilder
- StatementGroupStartBuilder
- TraitBuilder
- DatabaseInformationBuilder
- TimestampsBuilder
- ConstantsBuilder
- PreservedLinesBuilder
- StatementGroupEndBuilder

Example of replacing "DeclareStrictBuilder".
```php
    // Builders.
    'builders' => [
        // '{new-builder}' => '{exsting-builder}'
        MyDeclareStrictBuilder::class => DeclareStrictBuilder::class
    ],
```


## Examples

Generated model from table status with config.

```php
<?php

declare(strict_types=1);

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name Code for constants etc.
 * @property int $value Value to have fun with.
 */
class Status extends Model
{
    // Database.
    protected $connection = 'main';
    protected $table = 'status';

    // Timestamps.
    public $timestamps = false;

    // Constants.
    public const CONSTANT1 = 1;
    public const CONSTANT2 = 2;
    public const CONSTANT3 = 3;
    public const CONSTANT4 = 4;

    /* ---- Everything after this line will be preserved. ---- */

    /**
     * Preserve this method.
     *
     * @return string
     */
    public function preserveThisMethod()
    {
        return 'preserved';
    }
}
```


## Why the package has been rewritten from scratch
This project started as a help to a friend, but has since become a project
used by many. Therefore, the choice was made to rewrite the package from
the ground up, to enable code generation at all levels and to meet future
challenges.

It is possible to replace any part of code generation by either expanding
a builder or implementing an interface.

This rewrite contains changes that will break your code. The configuration
has changed, but it should be easy to convert to new version (v2). The config file
 has been renamed to "lmodel.php" so it is easy to compare changes.

It is no longer possible to specify guarded fields from the command line. However,
it is possible to specify guarded fields in configuration file.
