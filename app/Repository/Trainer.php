<?php

namespace App\Repository;

use App\Services\FirebaseService;

class Trainer implements TrainerInterface
{
    protected FirebaseService $firebase;
    protected $firebaseApiKey;

    public function __construct(FirebaseService $firebase, $firebaseApiKey = null)
    {
        $this->firebase = $firebase;
        $this->firebaseApiKey = env('FIREBASE_API_KEY');
    }

    public function getAllTrainers(array $filters)
    {
        $page = $filters['page'] ?? 1;
        $perPage = $filters['perPage'] ?? 10;

        $collection = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->where('role', '=', 'trainer')
            ->limit($perPage);

        if ($page > 1) {
            $skipDocs = $this->firebase->firestore()
                ->database()
                ->collection('users')
                ->where('role', '=', 'trainer')
                ->limit(($page - 1) * $perPage)
                ->documents();

            $last = null;
            foreach ($skipDocs as $d) {
                $last = $d;
            }

            if ($last) {
                $collection = $collection->startAfter($last);
            }
        }

        $docs = $collection->documents();

        $trainers = [];
        foreach ($docs as $doc) {
            if ($doc->exists()) {
                $trainers[] = array_merge($doc->data(), [
                    'uid' => $doc->id(),
                ]);
            }
        }

        return [
            'data' => $trainers,
            'page' => $page,
            'perPage' => $perPage,
            'nextPage' => count($trainers) === $perPage ? $page + 1 : null,
            'prevPage' => $page > 1 ? $page - 1 : null,
        ];
    }

}