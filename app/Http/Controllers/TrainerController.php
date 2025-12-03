<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repository\TrainerInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class TrainerController extends Controller
{
    protected $trainer;
    public function __construct(TrainerInterface $trainer)
    {
        $this->trainer = $trainer;
    }

    public function index(Request $request)
    {
        $filters = [
            'page' => $request->get('page', 1),
            'perPage' => 6,
            'fullname' => $request->get('fullname'),
            'category' => $request->get('category'),
            'location' => $request->get('location'),
        ];

        $trainers = $this->trainer->getAllTrainers($filters);

        return Inertia::render('Trainers', [
            'trainers' => $trainers,
            'filters' => $filters,
        ]);
    }
    
    public function show($uid)
    {
        return Inertia::render('TrainerProfile',[
            'trainer' => $this->trainer->getTrainer($uid)
        ]); 
    }

    public function submitReview(Request $request, $id)
    {
        $this->trainer->submitReview($id, $request->all());
        return Inertia::location('/trainer/' . $id);
    }

}
