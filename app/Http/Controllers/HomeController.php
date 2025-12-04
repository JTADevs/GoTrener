<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repository\TrainerInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    protected $trainer;
    public function __construct(TrainerInterface $trainer)
    {
        $this->trainer = $trainer;
    }
    
    public function index()
    {
        return Inertia::render('Home', [
            'premiumTrainers' => $this->trainer->getPremiumTrainers(4),
        ]);
        // return inertia('Home');
    }
}
