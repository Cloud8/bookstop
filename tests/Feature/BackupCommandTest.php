<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Config\Config;
use Tests\TestCase;

class BackupCommandTest extends TestCase
{
    private string $dbPath;

    private string $fakeBinDir;

    private string $fakeSqlite3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbPath = storage_path('app/test-backup-db.sqlite');
        $this->fakeBinDir = storage_path('app/fake-bin');
        $this->fakeSqlite3 = $this->fakeBinDir.'/sqlite3';

        touch($this->dbPath);
        mkdir($this->fakeBinDir, 0o755, true);
        file_put_contents($this->fakeSqlite3, "#!/bin/sh\necho 'BEGIN IMMEDIATE;'\necho 'COMMIT;'\nexit 0\n");
        chmod($this->fakeSqlite3, 0o755);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->fakeSqlite3)) {
            unlink($this->fakeSqlite3);
        }
        if (is_dir($this->fakeBinDir)) {
            rmdir($this->fakeBinDir);
        }
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }

        parent::tearDown();
    }

    public function test_backup_run_only_db_exits_successfully(): void
    {
        config([
            'database.connections.sqlite.database' => $this->dbPath,
            'database.connections.sqlite.dump' => [
                'dumpBinaryPath' => $this->fakeBinDir.'/',
            ],
            'backup.backup.source.databases' => ['sqlite'],
            'backup.backup.destination.disks' => ['local'],
            'backup.backup.destination.path' => 'test-backups',
        ]);

        $this->app->forgetScopedInstances();
        $this->app->forgetInstance(Config::class);

        Storage::fake('local');

        $this->artisan('backup:run --only-db --disable-notifications')
            ->assertExitCode(0);
    }
}
