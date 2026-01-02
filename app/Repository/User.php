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

        $user = array_merge($user, ['currentDimensions' => $this->firebase->firestore()
                ->database()
                ->collection('users')
                ->document($uid)
                ->collection('scores')
                ->document('currentDimensions')
                ->snapshot()
                ->data() ?? []]);

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
        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->set([
                'statsUpdatePeriod' => $data['period'],
                'statsUpdatedAt' => now()->toDateString(),
            ], ['merge' => true]);

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->collection('statsHistory')
            ->add([
                'created_at' => now(),
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
            ]);
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
        $userUid = $data['uid']; // This is the client
        $trainerUid = $data['trainerUid'];

        $trainingData = [
            'title'       => $data['title'],
            'date'        => $data['date'],
            'startTime'   => $data['startTime'],
            'endTime'     => $data['endTime'],
            'description' => $data['description'],
            'created_at'  => now(),
            'status'      => 'Planowany',
        ];

        // Add to client's collection
        $this->firebase->firestore()->database()
            ->collection('users')
            ->document($userUid)
            ->collection('trainings')
            ->add($trainingData + ['uid' => $trainerUid]); // Add trainer's UID

        // Add to trainer's collection
        $this->firebase->firestore()->database()
            ->collection('users')
            ->document($trainerUid)
            ->collection('trainings')
            ->add($trainingData + ['uid' => $userUid]); // Add client's UID

        return true;
    }

    public function getTrainings(string $uid)
    {
        $trainingsSnapshot = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->collection('trainings')
            ->orderBy('date', 'desc')
            ->documents();

        $trainings = [];
        foreach ($trainingsSnapshot as $document) {
            $training = $document->data();
            $training['id'] = $document->id();

            if (isset($training['uid'])) {
                $otherUser = $this->getUserByUid($training['uid']);
                if ($otherUser) {
                    $training['otherUser'] = $otherUser;
                }
            }
            
            $trainings[] = $training;
        }

        return $trainings;
    }

    public function cancelTraining(array $data)
    {
        $trainingId = $data['id'];
        $otherUserUid = $data['uid'];
        $cancellerUid = session('loggedUser.uid');

        // Update the canceller's training document
        $cancellerTrainingRef = $this->firebase->firestore()->database()
            ->collection('users')
            ->document($cancellerUid)
            ->collection('trainings')
            ->document($trainingId);

        $cancellerTrainingDoc = $cancellerTrainingRef->snapshot();

        if (!$cancellerTrainingDoc->exists()) {
            // This shouldn't happen if the UI is correct
            return false;
        }

        $cancellerTrainingRef->set(['status' => 'Anulowany'], ['merge' => true]);

        // Now, find and update the other user's training document.
        $trainingData = $cancellerTrainingDoc->data();

        $otherUserTrainingsQuery = $this->firebase->firestore()->database()
            ->collection('users')
            ->document($otherUserUid)
            ->collection('trainings')
            ->where('date', '==', $trainingData['date'])
            ->where('startTime', '==', $trainingData['startTime'])
            ->where('endTime', '==', $trainingData['endTime'])
            ->where('uid', '==', $cancellerUid);

        $otherUserTrainings = $otherUserTrainingsQuery->documents();

        foreach ($otherUserTrainings as $docToUpdate) {
            $docToUpdate->reference()->set(['status' => 'Anulowany'], ['merge' => true]);
        }

        return true;
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
}