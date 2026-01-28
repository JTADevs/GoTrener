<?php

namespace App\Repository\Profile;

use App\Services\FirebaseService;
use Carbon\Carbon;

class Promotion implements PromotionInterface
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function promotion($data)
    {
        $packageId = $data['package']['id'];
        $userId = $data['user_id'];
        
        // Pobierz aktualne dane użytkownika
        $userRef = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($userId);
            
        $userSnapshot = $userRef->snapshot();
        $userData = $userSnapshot->data();

        // Sprawdź czy użytkownik ma już aktywne premium
        $currentPremiumDate = null;
        if (isset($userData['is_premium_date'])) {
            try {
                $parsedDate = Carbon::parse($userData['is_premium_date']);
                if ($parsedDate->isFuture()) {
                    $currentPremiumDate = $parsedDate;
                }
            } catch (\Exception $e) {
                // Błędny format daty, ignorujemy
            }
        }

        // Jeśli ma aktywne premium, przedłużamy od tej daty, w przeciwnym razie od teraz
        $date = $currentPremiumDate ? $currentPremiumDate->copy() : now();
        
        switch ($packageId) {
            case 'monthly':
                $date->addMonth();
                break;
            case 'quarterly':
                $date->addMonths(3);
                break;
            case 'semi_annual':
                $date->addMonths(6);
                break;
            case 'yearly':
                $date->addYear();
                break;
            default:
                $date->addMonth(); // Fallback
                break;
        }

        $userRef->set([
            'is_premium' => true,
            'is_premium_date' => $date->toDateTimeString()
        ], ['merge' => true]);
    }
}