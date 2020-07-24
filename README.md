# Laravel Model Generator

![License](https://img.shields.io/packagist/l/corex/lmodel.svg)
![Build Status](https://travis-ci.org/corex/lmodel.svg?branch=master)
![codecov](https://codecov.io/gh/corex/lmodel/branch/master/graph/badge.svg)

The purpose of this package is to make it easy to implement existing databases and help with model generation for project and package development.

Connect to your existing database and generate models based on existing schema.
- Support for "declare(strict_types=1);" (PSR-12).
- Support for multiple connections.
- Support for auto-completion via phpdoc properties.
- Support for custom code (preserved lines).
- Support for fillable fields.
- Support for guarded fields.
- Support for custom "extends".
- Support for multiple groups of constants in model.
- Support for custom "indent".
- Support for Doctrine type mapping ("enum" mapped to "string", ...).
- Support for PhpDoc type mapping.
- Support for ignored tables.
- Support for showing destination of generated models (-d|--destination).
- Support for showing content of generated models (-c|--console).
- Support for replacing builders.
- Support for package development via configuration.

Generating a model will always overwrite existing model, but every line
below "preserve" identifier, will be preserved. Uses and Traits will also be preserved.


## Breaking changes from V1 to V2.
The package has been rewritten and due to many new settings and features, all settings and features
has been moved to configuration file. This way it can follow the project and does not require a lot
of scripting.
- Requires php 7.2.5+ and Laravel 7.x
- Config file has been renamed to ```{root}/config/lmodel.php``` and new settings added.
- Removed option to specify guarded fields on commands. It is now possible to specify in config file.
- It is no longer needed to specify service provider in AppServiceProviders@register method. Service provider is detected through Laravel's Package Discovery mechanism.


## Installation
Run ```"composer require corex/lmodel --dev"```.

The ModelServiceProvider is detected through Laravel's Package Discovery mechanism, so be sure
to add "--dev" as this will prevent corex/lmodel to be installed in production.

Run ```"php artisan vendor:publish --tag=lmodel-config"``` to publish config file to ```{root}/config/lmodel.php```.

Modify ```{root}/config/lmodel.php``` to suit your needs.
Every part that is configurable is described inside "lmodel.php" so it should be easy to get started.
If you have the need, a fresh configuration file can found at [{package}/config/lmodel.php](config/lmodel.php).


## Replacing a builder
Following is a list of builders that can be replaced by extending existing
builder, extending BaseBuilder or implementing BuilderInterface. If you plan
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
    // Builders ['{new-builder}' => '{existing-builder}'].
    'builders' => [
        MyDeclareStrictBuilder::class => DeclareStrictBuilder::class
    ],
```


## Examples

### Generate models
Generate model but output to console instead of writing (-c/--console) for my_table on default connection.
```bash
php artisan make:models . my_table -c
```

Generate model but output name of model and filename (-d/--destination) for my_table on default connection.
```bash
php artisan make:models . my_table -d
```

Generate all models on default connection.
```bash
php artisan make:models . .
```

### Example of generated model
Generated model from table status on connection 'main'.

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
