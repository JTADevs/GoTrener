<?php

namespace App\Repository;

interface UserInterface
{
    public function dashboard(string $uid);
    public function update(array $data);
    public function updateScore(array $data);
}