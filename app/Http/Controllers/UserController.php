<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repository\UserInterface;
use App\Repository\ChatInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $user;
    protected $chat;
    public function __construct(UserInterface $user, ChatInterface $chat)
    {
        $this->user = $user;
        $this->chat = $chat;
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
            $path = $file->storeAs('avatars', $filename, 'public');
            $data['imageURL'] = asset('storage/' . $path);
        }
        
        $this->user->update($data);
        return redirect()->route('profile');
    }
    
    public function gallery(Request $request)
    {
        $gallery = [];
        if ($request->hasFile('photos')) {
            $uid = session('loggedUser.uid');
            Storage::disk('public')->deleteDirectory('gallery/' . $uid);
            foreach ($request->file('photos') as $photo) {
                $filename = uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('gallery/' . $uid, $filename, 'public');
                $gallery[] = asset('storage/' . $path);
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

    public function addDiet(Request $request)
    {
        $this->user->addDiet($request->all());
    }

    public function deleteDiet($id)
    {
        $this->user->deleteDiet($id);
    }
}
