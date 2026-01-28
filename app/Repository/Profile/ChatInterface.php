<?php

namespace App\Repository\Profile;

interface ChatInterface
{
    public function getConversations(string $uid);
    public function getMentees(string $uid);
}
