<?php

namespace Pavliq\LaravelBackupPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Pavliq\LaravelBackupPackage\Services\DatabaseBackupService;

class LaravelPackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DatabaseBackupService::class, function () {
            return new DatabaseBackupService();
        });
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/backup.php', 'backup',
        );
    }
}