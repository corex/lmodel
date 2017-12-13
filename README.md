# CoRex Laravel Model
Laravel Model (generator, constants, preserved lines, phpdoc).

Auto-generate models with constants, preserved lines, phpdoc, etc. for Laravel 5.

**_Versioning for this package follows http://semver.org/. Backwards compatibility might break on upgrade to major versions._**

Connects to your existing database and auto-generates models based on existing schema.
 - Support for multiple connections.
 - Support for auto-completion via magic properties (phpdoc).
 - Support for custom methods (preserved lines).
 - Support for guarded fields.
 - Support for custom "extends".
 - Support for extra field-attributes after magic properties.
 - Support for building constants in model.
 - Support for custom "indent".
 - Support for preserving $timestamps value.

**Note: Generating a model that already exists will overwrite existing model, but every line below "preserve" identifier, will be preserved.**


## Installation
Run ```"composer require corex/lmodel --dev"```.

Add a configuration-file config/corex/lmodel.php and add following code to it. Modify it to suit your needs.
```php
return [
    'path' => base_path('app/Models'),
    'namespace' => 'App\Models',
    'addConnection' => true,
    'extends' => \Illuminate\Database\Eloquent\Model::class,
    'indent' => "\t",
    'length' => 120,
    'const' => [
        '{connection}' => [
            '{table}' => [
                'id' => '{id}',
                'name' => '{name}',
                'prefix' => '{prefix}',
                'suffix' => '{suffix}',
                'replace' => [
                    'XXXX' => 'YYYY',
                ]
            ]
        ]
    ]
];
```
Note: old config/corex.php is still supported but not recommended.

Settings:
 - **path** - where models are saved.
 - **namespace** - namespace of models.
 - **addConnection** - true/false if name of database-connection should be applied to namespace/directory. Name will automatically be converted to PascalCase.
 - **extends** - Class to extend.
 - **indent** - (optional) String to use as indent i.e. "\t". Default 4 spaces.
 - **length** - (optional) Length of line before linebreak. Used in tables with many fields.
 - **const** - (optional) This section is used to specify connections and tables which should contains constants from content of table.
 - **{connection}** - (optional) Name of connection.
 - **{table}** - (optional) Name of table.
 - **{id}** - (required) Name of field to get value of constant.
 - **{name}** - (required) Name of field to get name of constant.
 - **{prefix}** - (optional) Prefix to add to each name of constant.
 - **{suffix}** - (optional) Suffix to add to each name of constant.
 - **replace** - (optional) Values to replace in name of constant.

To register it and make sure you have this option available for development only, add following code to AppServiceProviders@register method.
```php
if ($this->app->environment() == 'local') {
    $this->app->register('CoRex\Laravel\Model\ModelServiceProvider');
}
```


## Help
```php artisan help make:models```

Arguments:
 - connection: Name of connection to generate models from. It will be added to namespace/path for separation of models per connection. It is possible to disable this by setting addConnection to false. Specify "." to generate from default connection.
 - tables: Comma separated table names to generate. Specify "." to generate all.

Options:
 - guarded: Comma separated list of guarded fields.


## Examples

Generated model from table status with config.

```php
<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id [TYPE=INTEGER, NULLABLE=0, DEFAULT=""]
 * @property string $name [TYPE=STRING, NULLABLE=0, DEFAULT=""]
 * @property string $value [TYPE=STRING, NULLABLE=0, DEFAULT=""]
 */
class Status extends Model
{
    // Constants.
    const CONSTANT1 = 1;
    const CONSTANT2 = 2;
    const CONSTANT3 = 3;
    const CONSTANT4 = 4;

    // Attributes.
    public $timestamps = false;
    protected $connection = 'mysql';
    protected $table = 'status';
    protected $fillable = ['id', 'name', 'value'];
    protected $guarded = [];

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
