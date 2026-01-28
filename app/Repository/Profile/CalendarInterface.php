<?php

namespace App\Repository\Profile;

interface CalendarInterface
{
    public function getUser(string $uid);
    public function createEvent(array $data);
    public function deleteEvent(string $id);
}   