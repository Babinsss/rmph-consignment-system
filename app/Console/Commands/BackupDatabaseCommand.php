<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'app:backup-database {--keep=30 : Number of daily backups to keep}';

    protected $description = 'Create a full SQL backup of the RMPH consignment database';

    public function handle(): int
    {
        $this->info('Starting RMPH Consignment database backup...');

        $connection = config('database.default', 'mysql');
        $driver = config("database.connections.{$connection}.driver", 'mysql');

        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = Carbon::now('Asia/Manila')->format('Y-m-d_His');
        $filename = "rmph_consignment_backup_{$timestamp}.sql";
        $filepath = "{$backupDir}/{$filename}";

        if ($driver === 'sqlite') {
            $dbPath = config("database.connections.{$connection}.database");
            if ($dbPath === ':memory:') {
                file_put_contents($filepath, "-- RMPH Consignment In-Memory SQLite Backup\n-- Generated: {$timestamp}\n");
            } else {
                File::copy($dbPath, $filepath);
            }

            $size = round(filesize($filepath) / 1024, 2);
            $this->info("Backup created successfully: {$filename} ({$size} KB)");

            $keep = (int) $this->option('keep');
            $this->cleanOldBackups($backupDir, $keep);

            return Command::SUCCESS;
        }

        $dbHost = config("database.connections.{$connection}.host", '127.0.0.1');
        $dbPort = config("database.connections.{$connection}.port", '3306');
        $dbName = config("database.connections.{$connection}.database", 'rmph_consignment');
        $dbUser = config("database.connections.{$connection}.username", 'root');
        $dbPass = config("database.connections.{$connection}.password", '');

        $mysqldump = $this->findMysqldump();

        if (! $mysqldump) {
            $this->error('mysqldump binary not found. Please ensure MySQL is in your PATH or Laragon bin directory.');

            return Command::FAILURE;
        }

        $command = [
            $mysqldump,
            "--host={$dbHost}",
            "--port={$dbPort}",
            "--user={$dbUser}",
        ];

        if (filled($dbPass)) {
            $command[] = "--password={$dbPass}";
        }

        $command[] = '--single-transaction';
        $command[] = '--quick';
        $command[] = $dbName;

        $process = new Process($command);
        $process->setTimeout(300);

        $outputStream = fopen($filepath, 'w');
        $process->run(function ($type, $buffer) use ($outputStream) {
            fwrite($outputStream, $buffer);
        });
        fclose($outputStream);

        if (! $process->isSuccessful()) {
            $this->error('Backup failed: '.$process->getErrorOutput());
            File::delete($filepath);

            return Command::FAILURE;
        }

        $size = round(filesize($filepath) / 1024, 2);
        $this->info("Backup created successfully: {$filename} ({$size} KB)");

        // Clean up older backups
        $keep = (int) $this->option('keep');
        $this->cleanOldBackups($backupDir, $keep);

        return Command::SUCCESS;
    }

    protected function findMysqldump(): ?string
    {
        // 1. Check system PATH
        $pathOutput = shell_exec('where mysqldump 2>nul');
        if ($pathOutput) {
            $lines = explode("\n", trim($pathOutput));
            if (! empty($lines[0]) && file_exists(trim($lines[0]))) {
                return trim($lines[0]);
            }
        }

        // 2. Check Laragon default paths
        $laragonGlob = glob('C:/laragon/bin/mysql/*/bin/mysqldump.exe');
        if (! empty($laragonGlob) && file_exists($laragonGlob[0])) {
            return $laragonGlob[0];
        }

        // 3. Check common Windows paths
        $commonPaths = [
            'C:/Program Files/MySQL/MySQL Server 8.0/bin/mysqldump.exe',
            'C:/Program Files/MySQL/MySQL Server 8.4/bin/mysqldump.exe',
            'C:/xampp/mysql/bin/mysqldump.exe',
        ];

        foreach ($commonPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    protected function cleanOldBackups(string $directory, int $keepDays): void
    {
        $cutoff = Carbon::now()->subDays($keepDays)->getTimestamp();
        $files = glob("{$directory}/rmph_consignment_backup_*.sql");

        $deleted = 0;
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                File::delete($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->line("Purged {$deleted} backup file(s) older than {$keepDays} days.");
        }
    }
}
