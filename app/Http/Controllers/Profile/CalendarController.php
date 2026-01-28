<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Repository\Profile\CalendarInterface;

class CalendarController extends Controller
{
    protected $calendarRepository;

    public function __construct(CalendarInterface $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function index()
    {
        return Inertia::render('Profile/Calendar', [
            'user' => $this->calendarRepository->getUser(session('loggedUser.uid'))
        ]); 
    }
}
