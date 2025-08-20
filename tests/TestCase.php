<?php

namespace Pavliq\LaravelBackupPackage\Tests;

use Pavliq\LaravelBackupPackage\Providers\LaravelPackageServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPackageServiceProvider::class
        ];
    }
    protected function getEnvironmentSetUp($app):void
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'wizardForm_test'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', '')
        ]);

        $app['config']->set('backup.path', 'app/backups/db');
        $app['config']->set('backup.max_backups', 2);
    }
}