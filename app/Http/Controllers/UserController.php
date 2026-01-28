<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repository\UserInterface;
use App\Repository\ChatInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FirebaseService;

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
        if(session('loggedUser.role') == 'trainer')
        {
            $mentees = $this->chat->getMentees(session('loggedUser.uid'));
        }

        return Inertia::render('Profile/Profile', [
            'user' => $this->user->dashboard(session('loggedUser.uid')),
            'conversations' => $this->chat->getConversations(session('loggedUser.uid')),
            'mentees' => $mentees ?? [],
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
}
