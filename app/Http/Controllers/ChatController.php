<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\ChatInterface;
use App\Services\FirebaseService;
use Google\Cloud\Firestore\FieldValue;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ChatController extends Controller
{
    protected FirebaseService $firebaseService;
    protected ChatInterface $chatRepository;

    public function __construct(FirebaseService $firebaseService, ChatInterface $chatRepository)
    {
        $this->firebaseService = $firebaseService;
        $this->chatRepository = $chatRepository;
    }

    public function index()
    {
        $conversations = $this->chatRepository->getConversations(session('loggedUser.uid'));
        
        return Inertia::render('Profile/Communicator', [
            'conversations' => $conversations,
            'currentUser' => session('loggedUser'),
        ]);
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $senderId = session('loggedUser.uid');
        $recipientId = $request->input('recipient_id');
        
        $ids = [$senderId, $recipientId];
        sort($ids);
        $chatId = implode('_', $ids);

        $firestore = $this->firebaseService->firestore();
        $chatRef = $firestore->database()->collection('chats')->document($chatId);
        
        // Ensure the chat document exists and has participants for querying
        $chatRef->set([
            'participants' => $ids,
            'updated_at' => FieldValue::serverTimestamp(),
            'sender_id' => $senderId,
            'read' => $request->boolean('read', false),
        ], ['merge' => true]);

        $messagesCollection = $chatRef->collection('messages');

        $messagesCollection->add([
            'sender_id' => $senderId,
            'text' => $request->input('message'),
            'timestamp' => FieldValue::serverTimestamp(),
        ]);

        return response()->json(['status' => 'success']);
    }
}
