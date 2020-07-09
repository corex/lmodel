# Laravel Model Generator

![License](https://img.shields.io/packagist/l/corex/lmodel.svg)
![Build Status](https://travis-ci.org/corex/lmodel.svg?branch=master)
![codecov](https://codecov.io/gh/corex/lmodel/branch/master/graph/badge.svg)

**Note: Breaking changes from V1 to V2.**


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
- Support for showing generated models before writing (--dry-run).
- Support for replacing builders.

Note: Generating a model will always overwrite existing model, but every line
below "preserve" identifier, will be preserved. Uses and Traits will also be preserved.


## Installation
Run ```"composer require corex/lmodel --dev"```.

Run ```"php artisan vendor:publish --tag=lmodel-config"```.

Modify ```{root}/config/lmodel.php``` to suit your needs.

To register, and to make sure you have this option available for local mode only, add following code to AppServiceProviders@register method.
```php
if ($this->app->environment('local')) {
    $this->app->register(\CoRex\Laravel\Model\ModelServiceProvider::class);
}
```


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

### Generate model
Generate model (only show / dryrun) for my_table on default connection.
```bash
php artisan make:models . my_table --dryrun
```

Generate all models on default connection.
```bash
php artisan make:models . .
```

### Generated model
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
