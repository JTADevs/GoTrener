<?php

namespace App\Repository\Profile;

use App\Repository\Profile\CalendarInterface;
use App\Services\FirebaseService;

class Calendar implements CalendarInterface
{
    protected FirebaseService $firebase;
    protected $firebaseApiKey;

    public function __construct(FirebaseService $firebase, $firebaseApiKey = null)
    {
        $this->firebase = $firebase;
        $this->firebaseApiKey = env('FIREBASE_API_KEY');
    }
    
    public function getUser($uid)
    {
        $user = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->snapshot()
            ->data();

        if ($user !== null) {
            $user['uid'] = $uid;
        }

        $personalEventsSnapshot = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->collection('personalEvents')
            ->orderBy('selectedDate', 'ASC')
            ->documents();

        $personalEvents = [];
        foreach ($personalEventsSnapshot as $document) {
            $personalEvents[] = array_merge(
                $document->data(),
                ['id' => $document->id()]
            );
        }

        $user['personalEvents'] = $personalEvents;

        return $user;
    }
}