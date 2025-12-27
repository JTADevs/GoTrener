<?php

namespace App\Repository;

use App\Services\FirebaseService;
use Illuminate\Support\Arr;

class Chat implements ChatInterface
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function getConversations(string $uid)
    {
        $conversations = $this->firebase->firestore()->database()
            ->collection('chats')
            ->where('participants', 'array-contains', $uid)
            ->documents();

        $chatList = [];

        foreach ($conversations as $convo) {
            if (!$convo->exists()) {
                continue;
            }

            $convoData = $convo->data();
            
            // Find the other user's ID
            $otherUserId = null;
            foreach ($convoData['participants'] as $participant) {
                if ($participant !== $uid) {
                    $otherUserId = $participant;
                    break;
                }
            }

            if (!$otherUserId) {
                continue;
            }

            // Get other user's data
            $otherUserDoc = $this->firebase->firestore()->database()
                ->collection('users')
                ->document($otherUserId)
                ->snapshot();
            
            if (!$otherUserDoc->exists()) {
                continue;
            }
            $otherUserData = $otherUserDoc->data();

            // Get the last message
            $lastMessage = $this->firebase->firestore()->database()
                ->collection('chats')
                ->document($convo->id())
                ->collection('messages')
                ->orderBy('timestamp', 'DESC')
                ->limit(1)
                ->documents()
                ->rows();
            
            $lastMessageData = !empty($lastMessage) ? $lastMessage[0]->data() : null;

            $chatList[] = [
                'chat_id' => $convo->id(),
                'otherUser' => [
                    'uid' => $otherUserDoc->id(),
                    'name' => $otherUserData['name'] ?? 'Użytkownik',
                    'imageURL' => $otherUserData['imageURL'] ?? '/images/no_user.png',
                ],
                'last_message' => $lastMessageData,
                'updated_at' => $convoData['updated_at']
            ];
        }

        usort($chatList, function ($a, $b) {
            return $b['updated_at'] <=> $a['updated_at'];
        });

        return $chatList;
    }
}
