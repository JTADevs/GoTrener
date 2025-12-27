<?php

namespace App\Repository;

interface ChatInterface
{
    public function getConversations(string $uid);
}
