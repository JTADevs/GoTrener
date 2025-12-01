<?php

namespace App\Repository;

interface TrainerInterface
{
    public function getAllTrainers(array $filters);
}