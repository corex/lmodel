<?php

declare(strict_types=1);

use CoRex\Laravel\Model\Builders\DeclareStrictBuilder;
use Illuminate\Database\Eloquent\Model;
use Tests\CoRex\Laravel\Model\Helpers\FakeDeclareStrictBuilder;

return [
    // true/false if "declare(strict_types=1);" should be added.
    'declareStrict' => true,

    // Path where to store models (automatically created).
    'path' => __DIR__,

    // Namespace prefix.
    'namespace' => 'Tests\CoRex\Laravel\Model\Files',

    // true/false if connection name should be added to path/namespace for models.
    'addConnectionToNamespace' => true,

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
    'doctrine' => [
        'test-type' => 'string'
    ],

    // PhpDoc type mappings ['{database-type}' => '{php-var-type}'].
    'phpdoc' => [],

    // Builders ['{new-builder}' => '{existing-builder}'].
    'builders' => [
        FakeDeclareStrictBuilder::class => DeclareStrictBuilder::class
    ],

    // Tables to ignore on connection (migration table is automatically ignored).
    // ['{connection}' => ['{table}', '{table}']]
    'ignored' => [
        'testbench' => ['ltest', 'table2']
    ],

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
        ],

        'my/package' => [
            'package' => dirname(__DIR__, 2),
            'relative' => 'src/Models'
        ]
    ],

    // Tables to handle in a specific way.
    'tables' => [
        'testbench' => [
            'lmodel' => [
                // Customize the name of the column used to store the timestamp CREATED_AT,
                // Value null means do not handle.
                'created_at' => 'created_at_test',

                // Customize the name of the column used to store the timestamp UPDATED_AT,
                // Value null means do not handle.
                'updated_at' => 'updated_at_test',

                // The storage format of the model's date columns (null means do not handle).
                // Value null means do not handle.
                'date_format' => 'U',

                // List of fillable columns.
                'fillable' => ['code', 'number', 'string', 'unknown'],

                // List of guarded columns.
                'guarded' => ['code', 'number', 'string', 'unknown'],

                // List of readonly columns (PhpDoc @property-read).
                'readonly' => ['code'],

                // Constants (supports both [] and [[]]).
                'constants' => [
                    [
                        // Title of constant.
                        'title' => 'Constants for numbers.',

                        // Column containing name of constant.
                        'name' => 'code',

                        // Column containing value of constant.
                        'value' => 'number',

                        // Prefix of constant name.
                        'prefix' => 'NUM',

                        // Suffix of constant name.
                        'suffix' => 'S',

                        // Values to replace in constant name.
                        'replace' => [
                            'SE' => '>>',
                            'ON' => '<<'
                        ]
                    ],
                    [
                        // Title of constant.
                        'title' => 'Constants for strings.',

                        // Column containing name of constant.
                        'name' => 'code',

                        // Column containing value of constant.
                        'value' => 'string',

                        // Prefix of constant name.
                        'prefix' => 'STR',

                        // Suffix of constant name.
                        'suffix' => 'S',

                        // Values to replace in constant name.
                        'replace' => [
                            'SE' => '>>',
                            'ON' => '<<'
                        ]
                    ]
                ]
            ]
        ]
    ]
];
