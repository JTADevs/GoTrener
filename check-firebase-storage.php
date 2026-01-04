<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Firebase Storage Diagnostics ===\n\n";

// Check .env configuration
echo "1. Checking .env configuration:\n";
echo "   FIREBASE_STORAGE_BUCKET = " . (env('FIREBASE_STORAGE_BUCKET') ?: 'NOT SET') . "\n";
echo "   FIREBASE_CREDENTIALS = " . (env('FIREBASE_CREDENTIALS') ?: 'NOT SET') . "\n\n";

// Check config
echo "2. Checking config/firebase.php:\n";
echo "   default_bucket = " . (config('firebase.projects.app.storage.default_bucket') ?: 'NOT SET') . "\n\n";

// Try to get Firebase Service
try {
    echo "3. Testing Firebase Storage connection:\n";
    $firebase = app(\App\Services\FirebaseService::class);
    $storage = $firebase->storage();
    $bucket = $storage->getBucket();
    
    echo "   ✓ Storage initialized successfully\n";
    echo "   Bucket name: " . $bucket->name() . "\n";
    echo "   Bucket info: " . $bucket->info()['id'] . "\n\n";
    
    echo "4. Testing bucket access:\n";
    try {
        $objects = $bucket->objects(['maxResults' => 1]);
        $count = 0;
        foreach ($objects as $object) {
            $count++;
            break;
        }
        echo "   ✓ Bucket is accessible\n";
        echo "   Found objects: " . ($count > 0 ? 'Yes' : 'No (empty bucket)') . "\n";
    } catch (\Exception $e) {
        echo "   ✗ Error accessing bucket: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== End of diagnostics ===\n";
