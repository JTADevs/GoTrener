<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\Profile\PromotionInterface;
use App\Repository\Profile\UserInterface;
use Inertia\Inertia;

class PromotionController extends Controller
{
    protected $promotion;
    protected $user;

    public function __construct(PromotionInterface $promotion, UserInterface $user)
    {
        $this->promotion = $promotion;
        $this->user = $user;
    }

    public function index()
    {
        return Inertia::render('Profile/Promotion', [
            'user' => $this->user->getUserByUid(session('loggedUser.uid'))
        ]);
    }

    public function promotion(Request $request)
    {
        $this->promotion->promotion($request->all());
    }
}
