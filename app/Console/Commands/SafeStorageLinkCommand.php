<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SafeStorageLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link
                {--relative : Create the symbolic link using relative paths}
                {--force : Recreate existing symbolic links}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the symbolic links configured for the application (Safe for shared hosting / exec-disabled servers)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $links = $this->laravel['config']['filesystems.links'] ?? [
            public_path('storage') => storage_path('app/public'),
        ];

        foreach ($links as $link => $target) {
            if (file_exists($link) && ! $this->isRemovable($link)) {
                $this->components->error("The [$link] link already exists.");
                continue;
            }

            if ($this->option('force') && file_exists($link)) {
                $this->removeLink($link);
            }

            $success = false;

            // 1. Try native symlink if available and not Windows
            if (function_exists('symlink') && !windows_os()) {
                try {
                    if (@symlink($target, $link)) {
                        $success = true;
                        $this->components->info("The [$link] symlink has been connected to [$target].");
                    }
                } catch (\Throwable $e) {
                    // Fall through to directory copy
                }
            }

            // 2. If on Windows and exec is available, try mklink
            if (!$success && windows_os() && $this->isExecAvailable()) {
                try {
                    $mode = is_dir($target) ? 'J' : 'H';
                    exec("mklink /{$mode} " . escapeshellarg($link) . ' ' . escapeshellarg($target), $output, $returnCode);
                    if ($returnCode === 0) {
                        $success = true;
                        $this->components->info("The [$link] junction has been connected to [$target].");
                    }
                } catch (\Throwable $e) {
                    // Fall through to directory copy
                }
            }

            // 3. Fallback: Copy / Synchronize physical directory (exec-free & shared-hosting compatible)
            if (!$success) {
                if (!File::isDirectory($link)) {
                    File::makeDirectory($link, 0755, true, true);
                }

                if (File::isDirectory($target)) {
                    File::copyDirectory($target, $link);
                }

                $this->components->info("The [$link] directory has been created and files synchronized directly (Symlink/exec-free bypass).");
                $success = true;
            }

            // Also mirror storage/app/public/uploads into public/uploads
            $targetUploads = storage_path('app/public/uploads');
            $publicUploads = public_path('uploads');
            if (File::isDirectory($targetUploads)) {
                if (!File::isDirectory($publicUploads)) {
                    File::makeDirectory($publicUploads, 0755, true, true);
                }
                File::copyDirectory($targetUploads, $publicUploads);
            }
        }

        return self::SUCCESS;
    }

    protected function isRemovable(string $link): bool
    {
        return (bool) $this->option('force');
    }

    protected function removeLink(string $link): void
    {
        if (is_link($link)) {
            File::delete($link);
        } elseif (File::isDirectory($link)) {
            File::deleteDirectory($link);
        } else {
            File::delete($link);
        }
    }

    protected function isExecAvailable(): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $disabled = explode(',', (string) ini_get('disable_functions'));
        $disabled = array_map('trim', $disabled);

        return !in_array('exec', $disabled, true);
    }
}
