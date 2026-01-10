<?php

namespace App\Repository;

interface UserInterface
{
    public function dashboard(string $uid);
    public function getUserByUid(string $uid);
    public function update(array $data);
    public function updateGallery(array $data);
    public function updateScore(array $data);
    public function createEvent(array $data);
    public function deleteEvent(string $id);
    public function updateStats(array $data);
    public function resetStats(string $uid);
    public function addTraining(array $data);
    public function getTrainings(string $uid);
    public function cancelTraining(array $data);
    public function generateTrainingPDF(string $id);
    public function addDiet(array $data);
    public function getDiets(string $uid);
    public function deleteDiet(string $id);
    public function downloadDietPDF(string $id);
    public function addTrainingPlan(array $data);
    public function getTrainingPlans(string $uid);
    public function deleteTrainingPlan(string $id);
    public function downloadTrainingPlanPDF(string $id);
}