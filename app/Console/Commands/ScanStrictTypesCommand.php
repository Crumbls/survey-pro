<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class ScanStrictTypesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:strict-types {path : The path to scan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan PHP files for missing declare(strict_types=1); declarations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        if (!is_dir($path)) {
            $this->error("Error: '{$path}' is not a valid directory.");
            return 1;
        }

        $this->info("Scanning directory: {$path}");

        $finder = new Finder();
        $finder->files()
            ->in($path)
            ->name('*.php')
            ->sortByName();

        $totalPhpFiles = 0;
        $missingStrictTypes = [];

        $progressBar = $this->output->createProgressBar(count($finder));
        $progressBar->start();

        foreach ($finder as $file) {
            $totalPhpFiles++;
            $filePath = $file->getRealPath();
            $content = file_get_contents($filePath);

            // Check if the file contains declare(strict_types=1)
            if (!preg_match('/declare\s*\(\s*strict_types\s*=\s*1\s*\)\s*;/i', $content)) {
                $missingStrictTypes[] = $filePath;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Scan complete.");
        $this->info("Total PHP files found: {$totalPhpFiles}");
        $this->info("Files missing strict_types declaration: " . count($missingStrictTypes));

        if (count($missingStrictTypes) > 0) {
            $this->newLine();
            $this->info("Files missing declare(strict_types=1):");

            $headers = ['File Path'];
            $rows = array_map(function ($file) {
                return [$file];
            }, $missingStrictTypes);

            $this->table($headers, $rows);

            // Optionally add a command to fix the files
            if ($this->confirm('Would you like to add strict_types declarations to these files?')) {
                $this->fixFiles($missingStrictTypes);
            }
        }

        return 0;
    }

    /**
     * Add strict_types declarations to the specified files.
     *
     * @param array $files
     */
    protected function fixFiles(array $files)
    {
        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        $fixedCount = 0;

        foreach ($files as $file) {
            $content = file_get_contents($file);

            // Check if there's a PHP opening tag
            if (preg_match('/<\?php/', $content)) {
                // Add declaration after opening PHP tag
                $newContent = preg_replace(
                    '/<\?php/',
                    "<?php\ndeclare(strict_types=1);",
                    $content,
                    1
                );

                if (file_put_contents($file, $newContent)) {
                    $fixedCount++;
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Fixed {$fixedCount} files.");
    }
}
