<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repository\AuthInterface;
use Inertia\Inertia;

class AuthController extends Controller
{
    protected $auth;
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    public function index()
    {
        if(session('loggedUser.uid'))
        {
            return redirect('/');
        }
        return Inertia::render('Auth');
    }

    public function login(Request $request)
    {   
        $validate = $request->validate([
            'loginEmail' => 'required|email',
            'loginPassword' => 'required|min:8',
        ]);

        $data = [
            'email' => $request->loginEmail,
            'password' => $request->loginPassword
        ];

        $message = $this->auth->login($data);

        if(isset($message['error'])) {
            return redirect()->route('auth')->with(['loginError' => $message['error']]);
        }

        session()->put('loggedUser', $message);
        return redirect('/');
    }

    public function register(Request $request)
    {
        $validate = $request->validate([
            'registerName' => 'required|string|max:255|min:3',
            'registerEmail' => 'required|email|unique:users,email',
            'registerPassword' => 'required|min:8',
            'role' => 'required|in:client,trainer'
        ]);

        $data = [
            'name' => $request->registerName,
            'email' => $request->registerEmail,
            'password' => $request->registerPassword,
            'role' => $request->role,
        ];


        $message = $this->auth->register($data);

        if(isset($message['error'])) {
            return redirect()->route('auth')->with(['registerError' => $message['error']]);
        }

        return redirect()->route('auth')->with(['registerSuccess' => $message['success']]);
    }

    public function confirm_account(Request $request)
    {
        $data = [
            'token' => $request->query('token'),
            'id' => $request->query('id'),
        ];

        $message = $this->auth->confirm($data);

        if(isset($message['error'])) {
            return redirect()->route('auth')->with(['registerError' => $message['error']]);
        }

        return redirect()->route('auth')->with(['registerSuccess' => $message['success']]);

    }

    public function logout()
    {
        session()->flush();
        return Inertia::location('/auth');
    }

}
