<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseService;

class MigrateToFirebaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-to-firebase 
                            {--dry-run : Run without actually uploading files}
                            {--path= : Specific path to migrate (e.g., avatars, gallery)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from local storage to Firebase Storage';

    protected FirebaseService $firebase;

    /**
     * Execute the console command.
     */
    public function handle(FirebaseService $firebase): int
    {
        $this->firebase = $firebase;
        $dryRun = $this->option('dry-run');
        $specificPath = $this->option('path');

        $this->info('Starting migration to Firebase Storage...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be uploaded');
        }

        // Define paths to migrate
        $paths = $specificPath ? [$specificPath] : ['avatars', 'gallery'];

        $totalFiles = 0;
        $successCount = 0;
        $errorCount = 0;

        foreach ($paths as $path) {
            $this->info("\nProcessing path: {$path}");
            
            if (!Storage::disk('public')->exists($path)) {
                $this->warn("Path {$path} does not exist in local storage. Skipping...");
                continue;
            }

            $files = Storage::disk('public')->allFiles($path);
            $this->info("Found " . count($files) . " files in {$path}");

            foreach ($files as $file) {
                $totalFiles++;
                
                try {
                    if (!$dryRun) {
                        // Get file content
                        $fileContent = Storage::disk('public')->get($file);
                        
                        // Create a temporary file
                        $tempPath = tempnam(sys_get_temp_dir(), 'firebase_migration_');
                        file_put_contents($tempPath, $fileContent);
                        
                        // Create UploadedFile instance
                        $uploadedFile = new \Illuminate\Http\UploadedFile(
                            $tempPath,
                            basename($file),
                            Storage::disk('public')->mimeType($file),
                            null,
                            true
                        );
                        
                        // Upload to Firebase
                        $url = $this->firebase->uploadFile($uploadedFile, $file);
                        
                        // Clean up temp file
                        unlink($tempPath);
                        
                        $this->line("✓ Uploaded: {$file} -> {$url}");
                        $successCount++;
                    } else {
                        $this->line("Would upload: {$file}");
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("✗ Failed to upload {$file}: " . $e->getMessage());
                    $errorCount++;
                }
            }
        }

        $this->newLine();
        $this->info('Migration Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Files', $totalFiles],
                ['Successful', $successCount],
                ['Failed', $errorCount],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a DRY RUN. Run without --dry-run to actually upload files.');
        } else {
            $this->info('Migration completed!');
            $this->warn('Remember to update your database URLs if needed.');
        }

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
