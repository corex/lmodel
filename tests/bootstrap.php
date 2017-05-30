<?php
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
} catch (Exception $e) {
    print($e->getMessage() . "\n");
    exit;
}
