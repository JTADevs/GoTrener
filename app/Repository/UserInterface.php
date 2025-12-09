<?php

namespace App\Repository;

interface UserInterface
{
    public function dashboard(string $uid);
    public function update(array $data);
    public function updateScore(array $data);
    public function createEvent(array $data);
    public function deleteEvent(string $id);
    public function updateStats(array $data);
}