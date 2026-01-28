<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Repository\Profile\UserInterface;
use App\Repository\Profile\ChatInterface;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
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
        return Inertia::render('Profile/Profile', [
            'user' => $this->user->dashboard(session('loggedUser.uid')),
            'conversations' => $this->chat->getConversations(session('loggedUser.uid')),
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

    public function updateStats(Request $request)
    {
        $this->user->updateStats($request->all());
    }

    public function resetStats()
    {
        $this->user->resetStats(session('loggedUser.uid'));
    }
}
