<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Firestore;
use Kreait\Firebase\Contract\Storage;

class FirebaseService
{
    protected Factory $factory;

    public function __construct()
    {
        $this->factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $bucket = env('FIREBASE_STORAGE_DEFAULT_BUCKET') ?? env('FIREBASE_STORAGE_BUCKET');
        
        if ($bucket) {
            $this->factory = $this->factory->withDefaultStorageBucket($bucket);
        }
    }

    public function auth(): Auth
    {
        return $this->factory->createAuth();
    }

    public function firestore(): Firestore
    {
        return $this->factory->createFirestore();
    }

    public function storage(): Storage
    {
        return $this->factory->createStorage();
    }

    public function uploadFile($file, string $path): string
    {
        $bucket = $this->storage()->getBucket();
        $object = $bucket->upload(
            fopen($file->getPathname(), 'r'),
            [
                'name' => $path,
                'predefinedAcl' => 'publicRead'
            ]
        );

        return "https://storage.googleapis.com/{$bucket->name()}/{$path}";
    }

    public function deleteDirectory(string $path): void
    {
        $bucket = $this->storage()->getBucket();
        $objects = $bucket->objects(['prefix' => $path]);

        foreach ($objects as $object) {
            $object->delete();
        }
    }
}
