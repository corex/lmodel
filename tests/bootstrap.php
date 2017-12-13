<?php

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

try {
    if (!file_exists(dirname(__DIR__) . '/database.php')) {
        $message = [
            'No ' . dirname(__DIR__) . '/database.php found.',
            'A valid database connection is required for testing this package.',
            'Copy database.dist.php to database.php and modify database connection.',
            '',
            'WARNING: ALL TABLES ARE DROPPED IF YOU EXECUTE TESTS ON SPECIFIED CONFIG!!!!'
        ];
        throw new Exception("\n" . implode("\n", $message) . "\n");
    }

    // Test connection.
    $app = new Container();
    $app->singleton('app', Container::class);
    $app->singleton('config', Repository::class);
    $config = require(dirname(__DIR__) . '/database.php');
    $connection = $config['default'];
    $app['config']->set('database.default', $connection);
    $app['config']->set('database.connections.' . $connection, $config['connections'][$connection]);
    $app->bind('db', function ($app) {
        return new DatabaseManager($app, new ConnectionFactory($app));
    });
    Facade::setFacadeApplication($app);
    DB::connection($connection)->getPdo();
} catch (Exception $e) {
    print($e->getMessage() . "\n");
    exit;
}
