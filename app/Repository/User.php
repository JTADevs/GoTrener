<?php

namespace App\Repository;

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
        $deletedEventRef = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->collection('personalEvents')
            ->document($id);
        
        $deletedEvent = $deletedEventRef->snapshot()->data();

        if(!empty($deletedEvent['trainingId'])){
            $this->firebase->firestore()->database()
                ->collection('trainings')
                ->document($deletedEvent['trainingId'])
                ->set(['status' => 'Anulowany'], ['merge' => true]);

            if (isset($deletedEvent['trainerUid']) && isset($deletedEvent['menteeUid'])) {
                $otherUid = ($deletedEvent['trainerUid'] == session('loggedUser.uid')) ? $deletedEvent['menteeUid'] : $deletedEvent['trainerUid'];

                if ($otherUid) {
                    $otherUserEvents = $this->firebase->firestore()
                        ->database()
                        ->collection('users')
                        ->document($otherUid)
                        ->collection('personalEvents')
                        ->where('trainingId', '=', $deletedEvent['trainingId'])
                        ->documents();

                    foreach ($otherUserEvents as $event) {
                        $event->reference()->delete();
                    }
                }
            }
        }

        $deletedEventRef->delete();

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

        $trainingId = Str::uuid() . '_' . $data['trainerUid'] . '_' . $data['uid'];

        $training = $this->firebase->firestore()->database()
            ->collection('trainings')
            ->document($trainingId)
            ->set($trainingData);

        $personalEvent = [
            'created_at' => now(),
            'eventTime' => $data['startTime'],
            'eventDateStart' => $data['date'],
            'eventDateEnd' => $data['date'],
            'selectedDate' => $data['date']
        ];
        
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($data['uid'])
            ->collection('personalEvents')
            ->add($personalEvent + [
                'eventDescription' => 'Trening z ' . $data['trainerName'],
                'trainerUid' => $data['trainerUid'],
                'menteeUid' => $data['uid'],
                'trainingId' => $trainingId,
            ]);

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($data['trainerUid'])
            ->collection('personalEvents')
            ->add($personalEvent + [
                'eventDescription' => 'Trening z ' . $data['menteeName'],
                'trainerUid' => $data['trainerUid'],
                'menteeUid' => $data['uid'],
                'trainingId' => $trainingId,
            ]);

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

        usort($trainings, function ($a, $b) {
            return strcmp($b['status'] ?? '', $a['status'] ?? '');
        });

        return $trainings;
    }

    public function cancelTraining(array $data)
    {
        $trainingId = $data['id'];
        $trainingRef = $this->firebase->firestore()->database()
            ->collection('trainings')
            ->document($trainingId);

        $trainingSnapshot = $trainingRef->snapshot();

        if (!$trainingSnapshot->exists()) {
            return false;
        }

        $trainingData = $trainingSnapshot->data();

        if (!empty($trainingData['menteeUid'])) {
            $menteeEvents = $this->firebase->firestore()->database()
                ->collection('users')
                ->document($trainingData['menteeUid'])
                ->collection('personalEvents')
                ->where('trainingId', '=', $trainingId)
                ->documents();

            foreach ($menteeEvents as $document) {
                $document->reference()->delete();
            }
        }

        if (!empty($trainingData['trainerUid'])) {
            $trainerEvents = $this->firebase->firestore()->database()
                ->collection('users')
                ->document($trainingData['trainerUid'])
                ->collection('personalEvents')
                ->where('trainingId', '=', $trainingId)
                ->documents();

            foreach ($trainerEvents as $document) {
                $document->reference()->delete();
            }
        }

        $trainingRef->set(['status' => 'Anulowany'], ['merge' => true]);

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

    public function addTrainingPlan(array $data)
    {
        $trainerUid = $data['trainerUid'];
        $menteeUid = $data['menteeUid'];

        $planData = [
            'trainerUid' => $trainerUid,
            'menteeUid' => $menteeUid,
            'trainerName' => $data['trainerName'],
            'menteeName' => $data['menteeName'],
            'title' => $data['title'],
            'description' => $data['description'],
            'plan' => $data['plan'],
            'created_at' => now(),
        ];

        $this->firebase->firestore()->database()
            ->collection('trainingPlans')
            ->document(Str::uuid() . '_' . $trainerUid . '_' . $menteeUid)
            ->set($planData);

        return true;
    }

    public function deleteTrainingPlan(string $id)
    {
        $this->firebase->firestore()
            ->database()
            ->collection('trainingPlans')
            ->document($id)
            ->delete();
        return true;
    }

    public function getTrainingPlans(string $uid)
    {
        $trainingPlansSnapshot = $this->firebase->firestore()
            ->database()
            ->collection('trainingPlans')
            ->documents();

        $trainingPlans = [];
        foreach ($trainingPlansSnapshot as $document) {
            if (str_contains($document->id(), $uid)) {
                $trainingPlan = $document->data();
                $trainingPlan['id'] = $document->id();
                $trainingPlans[] = $trainingPlan;
            }
        }

        return $trainingPlans;
    }

    public function downloadTrainingPlanPDF(string $id)
    {
        $planDoc = $this->firebase->firestore()
            ->database()
            ->collection('trainingPlans')
            ->document($id)
            ->snapshot();

        if (!$planDoc->exists()) {
            return null;
        }

        return $planDoc->data();
    }
}