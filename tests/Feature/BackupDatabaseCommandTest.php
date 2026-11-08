<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupDatabaseCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_command_runs_successfully_and_creates_sql_file(): void
    {
        $backupDir = storage_path('app/backups');

        $this->artisan('app:backup-database')
            ->assertSuccessful();

        $this->assertTrue(File::exists($backupDir));
        $backups = File::glob("{$backupDir}/rmph_consignment_backup_*.sql");
        $this->assertNotEmpty($backups);

        // Verify file is not empty
        $this->assertGreaterThan(0, filesize($backups[0]));
    }
}
