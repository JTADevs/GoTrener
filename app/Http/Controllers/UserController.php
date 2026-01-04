<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repository\UserInterface;
use App\Repository\ChatInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Services\FirebaseService;
use Spatie\Browsershot\Browsershot;

class UserController extends Controller
{
    protected $user;
    protected $chat;
    protected $firebase;
    public function __construct(UserInterface $user, ChatInterface $chat, FirebaseService $firebase)
    {
        $this->user = $user;
        $this->chat = $chat;
        $this->firebase = $firebase;
    }

    public function dashboard(Request $request)
    {
        if(!session('loggedUser.uid'))
        {
            return redirect()->route('auth')->with(['loginError'=>'Zaloguj się aby otrzymać dostęp do tego zasobu.']);
        }

        if(session('loggedUser.role') == 'trainer')
        {
            $mentees = $this->chat->getMentees(session('loggedUser.uid'));
        }

        return Inertia::render('Profile', [
            'user' => $this->user->dashboard(session('loggedUser.uid')),
            'conversations' => $this->chat->getConversations(session('loggedUser.uid')),
            'mentees' => $mentees ?? [],
            'trainings' => $this->user->getTrainings(session('loggedUser.uid')),
            'diets' => $this->user->getDiets(session('loggedUser.uid')),
            'view' => $request->query('view'),
        ]);
    }

    public function fetchConversations()
    {
        return response()->json($this->chat->getConversations(session('loggedUser.uid')));
    }

    public function update(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uid = session('loggedUser.uid');
            $extension = $file->getClientOriginalExtension();
            $filename = "{$uid}." . $extension;
            $data['imageURL'] = $this->firebase->uploadFile($file, "avatars/{$filename}");
        }
        
        $this->user->update($data);
        return redirect()->route('profile');
    }
    
    public function gallery(Request $request)
    {
        $gallery = [];
        if ($request->hasFile('photos')) {
            $uid = session('loggedUser.uid');
            $this->firebase->deleteDirectory('gallery/' . $uid);
            foreach ($request->file('photos') as $photo) {
                $filename = uniqid() . '.' . $photo->getClientOriginalExtension();
                $gallery[] = $this->firebase->uploadFile($photo, "gallery/{$uid}/{$filename}");
            }
        }
        $this->user->updateGallery(['gallery' => $gallery]);
        return redirect()->route('profile');
    }

    public function updateScore(Request $request)
    {
        $this->user->updateScore($request->all());
        return redirect()->route('profile');    
    }

    public function createEvent(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = session('loggedUser.uid');
        $this->user->createEvent($data);
    }

    public function deleteEvent($id)
    {
        $this->user->deleteEvent($id);
    }

    public function updateStats(Request $request)
    {
        $this->user->updateStats($request->all());
    }

    public function resetStats()
    {
        $this->user->resetStats(session('loggedUser.uid'));
    }

    public function addTraining(Request $request)
    {
        $this->user->addTraining($request->all());
    }

    public function cancelTraining(Request $request)
    {
        $this->user->cancelTraining($request->all());
    }

    public function generateTrainingPDF($id)
    {
        $training = $this->user->generateTrainingPDF($id);
        $training['id'] = $id;

        $parts = explode('_', $id);
        if (count($parts) >= 3) {
            $trainerUid = $parts[1];
            $menteeUid = $parts[2];
            $loggedUid = session('loggedUser.uid');

            $otherUid = ($loggedUid === $trainerUid) ? $menteeUid : $trainerUid;
            $otherUser = $this->user->getUserByUid($otherUid);

            $roleLabel = ($loggedUid === $trainerUid) ? 'Podopieczny' : 'Trener';
            $otherUser['roleLabel'] = $roleLabel;
            
            $training['otherUser'] = $otherUser;
        }

        $today = date('Y-m-d');
        if (isset($training['status']) && $training['status'] === 'Planowany' && isset($training['date']) && $training['date'] < $today) {
            $training['status'] = 'Ukończony';
        }

        return Pdf::view('training', ['training' => $training])
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot->timeout(120);
                $browsershot->noSandbox();
            })
            ->download('training.pdf');
    }

    public function addDiet(Request $request)
    {
        $this->user->addDiet($request->all());
    }

    public function deleteDiet($id)
    {
        $this->user->deleteDiet($id);
    }

    public function downloadDietPDF($id)
    {
        $data = $this->user->downloadDietPDF($id);
        return Pdf::view('diet', ['diet' => $data])->download('diet.pdf');
    }
}
