# Laravel Model Generator

![License](https://img.shields.io/packagist/l/corex/lmodel.svg)
![Build Status](https://travis-ci.org/corex/lmodel.svg?branch=master)
![codecov](https://codecov.io/gh/corex/lmodel/branch/master/graph/badge.svg)

**Note: Breaking changes from V1 to V2.**
- Config file has been renamed.
- Removed option to specify guarded fields on commands, since it is possible to specify in config file.

The purpose of this package is to make it easy to implement existing databases and help with package development.

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


## Installation
Run ```"composer require corex/lmodel --dev"```.

The ModelServiceProvider is detected through Laravel's Package Discovery mechanism, so be sure
to add "--dev" as this will prevent corex/lmodel to be installed in production.

Run ```"php artisan vendor:publish --tag=lmodel-config"``` to publish config file to ```{root}/config/lmodel.php```.

Modify ```{root}/config/lmodel.php``` to suit your needs.
Every part that is configurable is described inside "lmodel.php" so it should be easy to get started.


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
    // Builders.
    'builders' => [
        // '{new-builder}' => '{exsting-builder}'
        MyDeclareStrictBuilder::class => DeclareStrictBuilder::class
    ],
```


## Content of configuration file.
```php
<?php

use Illuminate\Database\Eloquent\Model;

return [
    // true/false if "declare(strict_types=1);" should be added.
    'declareStrict' => true,

    // Path where to store models (automatically created).
    'path' => base_path('app/Models'),

    // Namespace prefix.
    'namespace' => 'App\Models',

    // true/false if connection name should be added to path/namespace for models.
    'addConnectionToNamespace' => false,

    // true/false if connection should be set on model.
    'addDatabaseConnection' => true,

    // true/false if table should be set on model.
    'addDatabaseTable' => true,

    // Which class to extend. Default Eloquent model.
    'extends' => Model::class,

    // Indentation of lines inside model classes (null = 4 spaces).
    'indent' => null,

    // Max line length.
    'maxLineLength' => 120,

    // Doctrine type mappings ['database-type' => 'doctrine-type'].
    'doctrine' => [],

    // PhpDoc type mappings ['{database-type}' => '{php-var-type}'].
    'phpdoc' => [],

    // Builders ['{exsting-builder}' => '{new-builder}'].
    'builders' => [],

    // Tables to ignore on connection (migration table is automatically ignored).
    // ['{connection}' => ['{table}', '{table}']]
    'ignored' => [],

    // Package definitions.
    'packages' => [

        // Package ({vendor}/{project}).
        '{package}' => [

            // Absolute path to root of package where composer.json lives.
            'package' => '{path-to-package}',

            // Relative path from root of package where models should be generated.
            'relative' => '{relative-path-to-models}',

            // Patterns for matching tables belonging to this package.
            'patterns' => ['{table-matching-pattern}', '{table-matching-pattern}']
        ]
    ],

    // Tables to handle in a specific way (both project and package tables).
    'tables' => [
        '{connection}' => [
            '{table}' => [
                // Customize the name of the column used to store the timestamp CREATED_AT,
                // Value null means do not handle.
                'created_at' => null,

                // Customize the name of the column used to store the timestamp UPDATED_AT,
                // Value null means do not handle.
                'updated_at' => null,

                // The storage format of the model's date columns.
                // Value null means do not handle.
                'date_format' => null,

                // List of fillable columns.
                'fillable' => [],

                // List of guarded columns.
                'guarded' => [],

                // List of readonly columns (PhpDoc @property-read).
                'readonly' => [],

                // Constants.
                'constants' => [
                    [
                        // Title of constant.
                        'title' => '{title}',

                        // Column containing name of constant.
                        'name' => '{name}',

                        // Column containing value of constant.
                        'value' => '{value}',

                        // Prefix of constant name.
                        'prefix' => '{prefix}',

                        // Suffix of constant name.
                        'suffix' => '{suffix}',

                        // Values to replace in constant name.
                        'replace' => [
                            '{from}}' => '{to}',
                        ]
                    ]
                ]
            ]
        ]
    ]
];
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
