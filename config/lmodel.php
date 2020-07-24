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

    // Builders ['{new-builder}' => '{existing-builder}'].
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
