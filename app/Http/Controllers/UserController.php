<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repository\UserInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $user;
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function dashboard(Request $request)
    {
        if(!session('loggedUser.uid'))
        {
            return redirect()->route('auth')->with(['loginError'=>'Zaloguj się aby otrzymać dostęp do tego zasobu.']);
        }

        return Inertia::render('Profile', [
            'user' => $this->user->dashboard(session('loggedUser.uid')),
        ]);
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
        return Inertia::location(route('profile'));
    }

    public function deleteEvent($id)
    {
        $this->user->deleteEvent($id);
        return Inertia::location(route('profile'));
    }

    public function updateStats(Request $request)
    {
        $this->user->updateStats($request->all());
        return Inertia::location(route('profile'));
    }
}
