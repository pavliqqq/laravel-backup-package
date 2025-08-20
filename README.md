# Laravel Backup Package

1. Installation

```bash
composer require pavliq/laravel-backup-package:dev-master
```

2. Create an artisan command
```bash
php artisan make:command BackupDatabase
```

3. Open the generated file and add
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Pavliq\LaravelBackupPackage\Services\DatabaseBackupService;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup';

    public function handle(DatabaseBackupService $service)
    {
        $service->backupDatabase();
    }
}
```
4. Run the command
```bash
php artisan app:backup
```

5. Optional: Limit the number of backups

You can set a `MAX_BACKUPS` variable in your `.env` file to limit the number of stored backup files.

By default, it keeps 5 backups:
```
MAX_BACKUPS=5
```

Database backups will be created in `storage/app/backups/db`