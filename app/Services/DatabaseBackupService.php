<?php

namespace Pavliq\LaravelBackupPackage\Services;

use Illuminate\Support\Facades\File;
use Pavliq\LaravelBackupPackage\Dumpers\MysqlDumper;
use Pavliq\LaravelBackupPackage\Interfaces\DumpInterface;

class DatabaseBackupService
{
    protected DumpInterface $dumper;
    protected string $database;
    protected string $pathToDumps;

    public function __construct()
    {
        $this->database = config('database.default');
        $config = config("database.connections.$this->database");

        $this->dumper = match ($this->database) {
            'mysql' => new MysqlDumper($config)
        };

        $this->pathToDumps = storage_path(config('backup.path'));
    }

    public function backupDatabase(): void
    {
        $this->createDumpsFolder($this->pathToDumps);

        $fullPath = $this->pathToFile();

        $command = $this->dumper->dump($fullPath);
        shell_exec($command);

        echo "\nDump successfully created at $fullPath\n";

        $this->maxFilesHandler();
    }

    public function pathToFile(): string
    {
        $fileName = $this->database . '_backup' . date("_Y_m_d_H_i") . '.sql';
        return $this->pathToDumps . '/' . $fileName;
    }

    public function maxFilesHandler(): void
    {
        $max = config('backup.max_backups');

        if ($max) {
            $files = array_filter(File::files($this->pathToDumps), fn($f) => $f->getExtension() === 'sql');

            usort($files, fn($a, $b) => $a->getMTime() <=> $b->getMTime());

            if (count($files) > $max) {
                $filesToDelete = array_slice($files, 0, count($files) - $max);
                $deletedFiles = count($filesToDelete);

                foreach ($filesToDelete as $file) {
                    File::delete($file->getPathname());
                }
                echo "\nDeleted $deletedFiles files\n";
            }
        }
    }

    public function createDumpsFolder($path): void
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        echo "\nCreated folders: " . config('backup.path') . " at " . storage_path() . "\n";
    }
}