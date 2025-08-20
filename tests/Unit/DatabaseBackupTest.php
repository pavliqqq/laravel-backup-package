<?php

namespace Pavliq\LaravelBackupPackage\Tests\Unit;

use Illuminate\Support\Facades\File;
use Pavliq\LaravelBackupPackage\Services\DatabaseBackupService;
use Pavliq\LaravelBackupPackage\Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DatabaseBackupService();

        $this->path = storage_path(config('backup.path'));

        if (file_exists($this->path)) {
            foreach (File::files($this->path) as $file) {
                File::delete($file);
            }
        } else {
            mkdir($this->path, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->path)) {
            File::deleteDirectory($this->path);
        }

        parent::tearDown();
    }

    public function test_creates_a_database_dump_file()
    {
        $files = File::files($this->path);
        $this->assertEmpty($files);

        $this->service->backupDatabase();

        $files = File::files($this->path);
        $this->assertNotEmpty($files);
    }

    public function test_generates_correct_dump_file_name()
    {
        $pathToFile = $this->service->pathToFile();

        $this->assertStringContainsString('_backup', $pathToFile);
        $this->assertStringEndsWith('.sql', $pathToFile);
    }

    public function test_creates_dump_directory_if_missing()
    {
        File::deleteDirectory($this->path);

        $this->assertFalse(file_exists($this->path));

        $this->service->createDumpsFolder($this->path);
        $this->assertTrue(file_exists($this->path));
    }

    public function test_keeps_only_max_number_of_files()
    {
        $files = ['old1.sql', 'new2.sql', 'old3.sql', 'new4.sql'];

        foreach ($files as $file) {
            $filePath = $this->path . '/' . $file;
            File::put($filePath, 'test');
            if (str_contains($file, 'old')) {
                touch($filePath, time() - 3600);
            } else {
                touch($filePath, time());
            }
        }

        $this->service->maxFilesHandler();

        $max = config('backup.max_backups');

        $remainingFiles = array_map(fn($f) => $f->getFilename(), File::files($this->path));
        $this->assertCount($max, $remainingFiles);

        $this->assertContains('new2.sql', $remainingFiles);
        $this->assertContains('new4.sql', $remainingFiles);
        $this->assertNotContains('old1.sql', $remainingFiles);
        $this->assertNotContains('old3.sql', $remainingFiles);
    }
}