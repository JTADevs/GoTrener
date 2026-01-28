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
    
    public function getUser(string $uid)
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

    public function createEvent(array $data)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->collection('personalEvents')
            ->add([
                'selectedDate' => $data['selectedDate'],
                'eventTime' => $data['eventTime'],
                'eventDescription' => $data['eventDescription'],
                'created_at' => now(),
            ]);
        return true;    
    }

    public function deleteEvent(string $id)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->collection('personalEvents')
            ->document($id)
            ->delete();

        return true;    
    }
}