<?php

namespace App\Repository;

interface ChatInterface
{
    public function getConversations(string $uid);
    public function getMentees(string $uid);
}
