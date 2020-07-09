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

    // Doctrine type mappings.
    'doctrine' => [
        // 'database-type' => 'doctrine-type'
        'test-type' => 'string'
    ],

    // PhpDoc type mappings.
    'phpdoc' => [
        // '{database-type}' => '{php-var-type}'
    ],

    // Builders.
    'builders' => [
        // '{new-builder}' => '{exsting-builder}'
        FakeDeclareStrictBuilder::class => DeclareStrictBuilder::class
    ],

    // Ignored tables.
    'ignored' => [
        // Tables to ignore on connection (migration table is automatically ignored).
        // '{connection}' => ['{table}', '{table}']
        'testbench' => ['ltest', 'table2']
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
