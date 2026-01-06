<?php

namespace App\Repository;

use App\Services\FirebaseService;
use Google\Cloud\Firestore\FieldValue;
use Illuminate\Support\Str;

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
            ); // id should be YYYY-MM-DD
            $historyDates[$document->id()] = true;
        }

        // Add currentDimensions as a history point if it exists and date is not already in history (or if history is empty)
        // Ideally, currentDimensions IS the latest state, but if statsHistory is used, it should be in sync.
        // However, if currentDimensions represents "Start Data" (older data), it might not have a date effectively if not set.
        // If updated_at is present:
        if (!empty($currentDimensions) && isset($currentDimensions['updated_at'])) {
             // Handle Carbon or string
             try {
                $date = \Carbon\Carbon::parse($currentDimensions['updated_at'])->toDateString();
                if (!isset($historyDates[$date])) {
                     $currentDimensions['id'] = $date;
                     $statsHistory[] = $currentDimensions;
                }
             } catch (\Exception $e) {
                 // ignore date parse error
             }
        }
        
        // Sort by date (id)
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

    public function createEvent(array $data)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($data['user_id'])
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

    public function addTraining(array $data)
    {
        $trainingData = [
            'title'       => $data['title'],
            'date'        => $data['date'],
            'startTime'   => $data['startTime'],
            'endTime'     => $data['endTime'],
            'description' => $data['description'],
            'plan'        => $data['plan'],
            'menteeUid'   => $data['uid'],
            'trainerUid'  => $data['trainerUid'],
            'created_at'  => now(),
            'status'      => 'Planowany',
        ];

        $personalEvent = [
            'created_at' => now(),
            'eventTime' => $data['startTime'],
            'eventDateStart' => $data['date'],
            'eventDateEnd' => $data['date'],
            'selectedDate' => $data['date']
        ];

        $this->firebase->firestore()->database()
            ->collection('trainings')
            ->document(Str::uuid() . '_' . $data['trainerUid'] . '_' . $data['uid'])
            ->set($trainingData);

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($data['uid'])
            ->collection('personalEvents')
            ->add($personalEvent + ['eventDescription' => 'Trening z ' . $data['trainerName']]);

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($data['trainerUid'])
            ->collection('personalEvents')
            ->add($personalEvent + ['eventDescription' => 'Trening z ' . $data['menteeName']]);

        return true;
    }

    public function getTrainings(string $uid)
    {
        $trainingsSnapshot = $this->firebase->firestore()
            ->database()
            ->collection('trainings')
            ->documents();

        $trainings = [];
        $usersCache = [];

        foreach ($trainingsSnapshot as $document) {
            if (str_contains($document->id(), $uid)) {
                $training = $document->data();
                $training['id'] = $document->id();

                $parts = explode('_', $document->id());
                if (count($parts) >= 3) {
                    $trainerUid = $parts[1];
                    $menteeUid = $parts[2];
                    $otherUid = ($uid === $trainerUid) ? $menteeUid : $trainerUid;

                    if (!isset($usersCache[$otherUid])) {
                        $usersCache[$otherUid] = $this->getUserByUid($otherUid);
                    }
                    
                    $training['otherUser'] = $usersCache[$otherUid];
                }

                $trainings[] = $training;
            }
        }

        return $trainings;
    }

    public function cancelTraining(array $data)
    {
        $this->firebase->firestore()->database()
            ->collection('trainings')
            ->document($data['id'])
            ->set(['status' => 'Anulowany'], ['merge' => true]);

        return true;
    }

    public function generateTrainingPDF(string $id)
    {
        $trainingSnapshot = $this->firebase->firestore()
            ->database()
            ->collection('trainings')
            ->document($id)
            ->snapshot();

        if (!$trainingSnapshot->exists()) {
            return null;
        }

        $trainingData = $trainingSnapshot->data();
        
        return $trainingData;
    }

    public function addDiet(array $data)
    {
        $trainerUid = $data['trainerUid'];
        $menteeUid = $data['menteeUid'];

        $trainingData = [
            'trainerName' => $data['trainerName'],
            'menteeName' => $data['menteeName'],
            'dietDescription' => $data['dietDescription'],
            'caloricValue' => $data['caloricValue'],
            'created_at' => now(),
        ];
        
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($daysOfWeek as $day) {
            $trainingData[$day] = [
                'breakfast' => $data[$day]['breakfast'],
                'secondBreakfast' => $data[$day]['secondBreakfast'],
                'lunch' => $data[$day]['lunch'],
                'afternoonSnack' => $data[$day]['afternoonSnack'],
                'dinner' => $data[$day]['dinner'],
                'macro' => $data[$day]['macro'],
            ];
        }

        $this->firebase->firestore()->database()
            ->collection('diets')
            ->document(Str::uuid() . '_' . $trainerUid . '_' . $menteeUid)
            ->set($trainingData);

        return true;
    }

    public function getDiets(string $uid)
    {
        $dietsSnapshot = $this->firebase->firestore()
            ->database()
            ->collection('diets')
            ->documents();

        $diets = [];
        foreach ($dietsSnapshot as $document) {
            if (str_contains($document->id(), $uid)) {
                $diet = $document->data();
                $diet['id'] = $document->id();
                $diets[] = $diet;
            }
        }

        return $diets;
    }

    public function deleteDiet(string $id)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('diets')
            ->document($id)
            ->delete();
        return true;
    }

    public function downloadDietPDF(string $id)
    {
        $dietDoc = $this->firebase->firestore()
            ->database()
            ->collection('diets')
            ->document($id)
            ->snapshot();

        if (!$dietDoc->exists()) {
            return null;
        }

        $dietData = $dietDoc->data();

        return $dietData;
    }
}