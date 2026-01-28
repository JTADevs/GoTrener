<?php

namespace App\Repository\Profile;

use App\Services\FirebaseService;
use Google\Cloud\Firestore\FieldValue;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

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

        if ($user !== null) {
            $user['uid'] = $uid;
        }

        $currentDimensions = $this->firebase->firestore()
                ->database()
                ->collection('users')
                ->document($uid)
                ->collection('scores')
                ->document('currentDimensions')
                ->snapshot()
                ->data() ?? [];

        $user = array_merge($user, ['currentDimensions' => $currentDimensions]);

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

        $user = array_merge($user, ['personalEvents' => $personalEvents]);  

        $statsHistorySnapshot = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->collection('statsHistory')
            ->documents();

        $statsHistory = [];
        $historyDates = [];

        foreach ($statsHistorySnapshot as $document) {
            $statsHistory[] = array_merge(
                $document->data(),
                ['id' => $document->id()]
            );
            $historyDates[$document->id()] = true;
        }

        if (!empty($currentDimensions) && isset($currentDimensions['updated_at'])) {
             try {
                $date = \Carbon\Carbon::parse($currentDimensions['updated_at'])->toDateString();
                if (!isset($historyDates[$date])) {
                     $currentDimensions['id'] = $date;
                     $statsHistory[] = $currentDimensions;
                }
             } catch (\Exception $e) {}
        }
        
        usort($statsHistory, function($a, $b) {
            return strcmp($a['id'], $b['id']);
        });

        $user = array_merge($user, ['statsHistory' => $statsHistory]);

        return $user;
    }

    public function getUserByUid(string $uid)
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
                'motto' => $data['motto'] ?? '',
                'category' => $data['category'] ?? [],    
            ], ['merge' => true]); 
        return true;
    }

    public function updateGallery(array $data)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->set([
                'gallery' => $data['gallery'] ?? []
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



    public function updateStats(array $data)
    {
        $uid = session('loggedUser.uid');
        
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->set([
                'statsUpdatedAt' => now()->toDateString(),
            ], ['merge' => true]);

        $currentDate = now()->toDateString();
        
        $statsData = ['created_at' => now()];
        $fields = [
            'weight', 'height', 'neckCircumference', 'chestCircumference',
            'waistCircumference', 'abdomenCircumference', 'hipCircumference',
            'bicepsCircumference', 'wristCircumference', 'thighCircumference',
            'calfCircumference', 'ankleCircumference'
        ];

        foreach ($fields as $field) {
            if (isset($data[$field]) && $data[$field] !== '' && $data[$field] !== null) {
                $statsData[$field] = $data[$field];
            }
        }

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->collection('statsHistory')
            ->document($currentDate)
            ->set($statsData, ['merge' => true]);
            
        return true;    
    }

    public function resetStats(string $id)
    {
        $userRef = $this->firebase->firestore()->database()
                    ->collection('users')
                    ->document(session('loggedUser.uid'));
        $userRef->update([
            ['path' => 'statsUpdatePeriod', 'value' => FieldValue::deleteField()],
            ['path' => 'statsUpdatedAt', 'value' => FieldValue::deleteField()],
        ]);

         $statsHistory = $userRef->collection('statsHistory')->documents();

        foreach ($statsHistory as $doc) {
            if ($doc->exists()) {
                $doc->reference()->delete();
            }
        }

        return true;
    }
}