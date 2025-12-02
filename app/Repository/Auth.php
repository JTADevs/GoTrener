<?php

namespace App\Repository;

use App\Mail\AccountVerification;
use Illuminate\Support\Facades\Http;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Auth implements AuthInterface
{
    protected FirebaseService $firebase;
    protected $firebaseApiKey;

    public function __construct(FirebaseService $firebase, $firebaseApiKey = null)
    {
        $this->firebase = $firebase;
        $this->firebaseApiKey = env('FIREBASE_API_KEY');
    }

    public function login(array $data)
    {
        $response = Http::post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$this->firebaseApiKey}", [
            'email' => $data['email'],
            'password' => $data['password'],
            'returnSecureToken' => true,
        ]);

        if (!$response->successful()) {
            return ['error' => 'Nieprawidłowy email lub hasło'];
        }

        $loginData = $response->json();
        
        $userDoc = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($loginData['localId'])
            ->snapshot();

        $userData = $userDoc->exists() ? $userDoc->data() : [];

        if($userData['email_verified'] === false) {
            return ['error' => 'Konto nie zostało zweryfikowane. Sprawdź swoją skrzynkę e-mail.'];
        }

        return [
            'token' => $loginData['idToken'],
            'uid'   => $loginData['localId'],
            'email' => $loginData['email'],
            'name'  => $userData['name'] ?? null,
            'role'  => $userData['role'] ?? 'client',
        ];
    }


    public function register(array $data)
    {
        $checkUser = Http::post(
            "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$this->firebaseApiKey}",
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'returnSecureToken' => true,
            ]
        );

        if ($checkUser->successful()) {
            return ['error' => 'Użytkownik z tym adresem e-mail już istnieje.'];
        }

        $createUser = Http::post(
            "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key={$this->firebaseApiKey}",
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'returnSecureToken' => true,
            ]
        );

        if (!$createUser->successful()) {
            return ['error' => 'Nie udało się utworzyć konta. Spróbuj ponownie.'];
        }

        $verifyCode = Str::random(12);

        $createUserData = $createUser->json();

        $firestoreData = [
            '_token'     => $createUserData['idToken'],
            'role'       => $data['role'] ?? 'client',
            'email'      => $data['email'] ?? null,
            'name'       => $data['name'] ?? null,
            'email_verified' => false,
            'verifyCode' => $verifyCode,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        Mail::to($data['email'])->send(new AccountVerification($data['name'], $verifyCode, $createUserData['localId']));

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($createUserData['localId'])
            ->set($firestoreData);

        return ['success' => 'Konto utworzone pomyślnie! Aktywuj konto poprzez link wysłany na adres email.'];
    }

    public function confirm($data)
    {
        $token = $data['token'] ?? null;
        $id = $data['id'] ?? null;

        if (!$token || !$id) {
            return ['error' => 'Nieprawidłowy link weryfikacyjny.'];
        }

        $userDoc = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($id)
            ->snapshot();

        if (!$userDoc->exists()) {
            return ['error' => 'Nie znaleziono użytkownika.'];
        }

        $user = $userDoc->data();

        if (($user['verifyCode'] ?? '') !== $token) {
            return ['error' => 'Nieprawidłowy token weryfikacyjny.'];
        }

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($id)
            ->update([
                ['path' => 'email_verified', 'value' => true],
                ['path' => 'verifyCode', 'value' => null],
            ]);

        return ['success' => 'Twój adres e-mail został zweryfikowany! Możesz się teraz zalogować.'];
    }


}