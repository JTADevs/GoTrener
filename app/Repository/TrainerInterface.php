<?php

namespace App\Repository;

interface TrainerInterface
{
    public function getAllTrainers(array $filters);
    public function getTrainer(string $uid);
    public function submitReview(string $trainerId, array $data);
}