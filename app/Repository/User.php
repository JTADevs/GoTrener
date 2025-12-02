<?php

namespace App\Repository;

use App\Services\FirebaseService;

class User implements UserInterface
{
    protected FirebaseService $firebase;
    protected $firebaseApiKey;

    public function __construct(FirebaseService $firebase, $firebaseApiKey = null)
    {
        $this->firebase = $firebase;
        $this->firebaseApiKey = env('FIREBASE_API_KEY');
    }

    public function dashboard(string $uid)
    {
        $user = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->snapshot()
            ->data();

        $user = array_merge($user, ['currentDimensions' => $this->firebase->firestore()
                ->database()
                ->collection('users')
                ->document($uid)
                ->collection('scores')
                ->document('currentDimensions')
                ->snapshot()
                ->data() ?? []]);

        return $user;
    }

    public function update(array $data)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->set([
                'updated_at' => now(),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'gender' => $data['gender'],
                'imageURL' => $data['imageURL'] ?? null,
                'facebook' => $data['facebook'],
                'instagram' => $data['instagram'],
                'website' => $data['website'],
                'tiktok' => $data['tiktok'] ?? '',
                'youtube' => $data['youtube'] ?? '',
                'bio' => $data['bio'] ?? '',
                'category' => $data['category'] ?? [],    
            ], ['merge' => true]); 
        return true;
    }

    public function updateScore(array $data)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->collection('scores')
            ->document('currentDimensions')
            ->set([
                'updated_at' => now(),
                'weight' => $data['weight'],
                'height' => $data['height'],
                'neckCircumference' => $data['neckCircumference'],
                'chestCircumference' => $data['chestCircumference'],
                'waistCircumference' => $data['waistCircumference'],
                'abdomenCircumference' => $data['abdomenCircumference'],
                'hipCircumference' => $data['hipCircumference'],
                'bicepsCircumference' => $data['bicepsCircumference'],
                'wristCircumference' => $data['wristCircumference'],
                'thighCircumference' => $data['thighCircumference'],
                'calfCircumference' => $data['calfCircumference'],
                'ankleCircumference' => $data['ankleCircumference'],    
            ], ['merge' => true]);
        return true;    
    }

}