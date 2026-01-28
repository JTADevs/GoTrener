<?php

namespace App\Repository\Profile;

interface UserInterface
{
    public function dashboard(string $uid);
    public function getUserByUid(string $uid);
    public function update(array $data);
    public function updateGallery(array $data);
    public function updateScore(array $data);
    public function updateStats(array $data);
    public function resetStats(string $uid);
}