<?php

namespace App\Repository;

use App\Services\FirebaseService;

class Trainer implements TrainerInterface
{
    protected FirebaseService $firebase;
    protected $firebaseApiKey;

    public function __construct(FirebaseService $firebase, $firebaseApiKey = null)
    {
        $this->firebase = $firebase;
        $this->firebaseApiKey = env('FIREBASE_API_KEY');
    }

    public function getAllTrainers(array $filters)
    {
        $page = $filters['page'] ?? 1;
        $perPage = $filters['perPage'] ?? 10;
        $fullname = trim($filters['fullname'] ?? '');
        $category = trim($filters['category'] ?? '');

        $baseQuery = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->where('role', '=', 'trainer');

        $baseQuery = $baseQuery->orderBy('__name__');

        $docs = $baseQuery->documents();
        
        $allTrainers = [];
        foreach ($docs as $doc) {
            if ($doc->exists()) {
                $allTrainers[] = array_merge($doc->data(), ['uid' => $doc->id()]);
            }
        }

        $isSearching = !empty($fullname) || !empty($category);

        if ($isSearching) {
            $filteredTrainers = array_filter($allTrainers, function($trainer) use ($fullname, $category) {
                $nameMatches = true;
                $categoryMatches = true;
                
                if (!empty($fullname)) {
                    $name = strtolower($trainer['name'] ?? '');
                    $nameMatches = str_contains($name, strtolower($fullname));
                }

                if (!empty($category)) {
                    $trainerCategories = array_map('strtolower', $trainer['category'] ?? []);
                    $searchCategory = strtolower($category);
                    
                    $categoryMatches = false;
                    foreach ($trainerCategories as $cat) {
                        if (str_contains($cat, $searchCategory)) {
                            $categoryMatches = true;
                            break;
                        }
                    }
                }
                
                return $nameMatches && $categoryMatches;
            });

            $trainers = array_values($filteredTrainers);
        } else {
            $trainers = array_values($allTrainers);
        }

        $totalResults = count($trainers);
        
        $offset = ($page - 1) * $perPage;
        $paginatedTrainers = array_slice($trainers, $offset, $perPage);
        
        $paginatedTrainersWithReviews = [];
        foreach ($paginatedTrainers as $trainer) {
            $reviews = $this->firebase->firestore()
                ->database()
                ->collection('users')
                ->document($trainer['uid'])
                ->collection('reviews')
                ->documents()
                ->rows();
        
            $reviewData = [];
            foreach ($reviews as $review) {
                if ($review->exists()) {
                    $reviewData[] = $review->data();
                }
            }
            $trainer['reviews'] = $reviewData;
            $paginatedTrainersWithReviews[] = $trainer;
        }
        
        $nextPage = ($offset + $perPage) < $totalResults ? $page + 1 : null;
        $prevPage = $page > 1 ? $page - 1 : null;

        return [
            'data' => $paginatedTrainersWithReviews,
            'page' => $page,
            'perPage' => $perPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'fullname' => $fullname,
            'category' => $category,
        ];
    }

    public function getTrainer(string $uid)
    {
        $trainer = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->snapshot()
            ->data();

        $reviews = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($uid)
            ->collection('reviews')
            ->documents()
            ->rows();

        foreach($reviews as $review){
            $reviewData[] = $review->data();
        }
        
        $trainer = array_merge($trainer, ['uid' => $uid], ['reviews' => $reviewData ?? []]);
        return $trainer;
    }

    public function submitReview(string $trainerId, array $data)
    {
        $userName = $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document(session('loggedUser.uid'))
            ->snapshot()
            ->data()['name'] ?? 'Anonymous';

        $this->firebase->firestore()
            ->database()
            ->collection('users')
            ->document($trainerId)
            ->collection('reviews')
            ->document(session('loggedUser.uid'))
            ->set([
                'userId' => session('loggedUser.uid'),
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'userName' => $userName,
                'created_at' => now(),
            ]);
        return true;
    }

}