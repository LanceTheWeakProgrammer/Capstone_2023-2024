<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Booking;
use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }

    public function receiveMessages($userId)
    {
        $authUser = auth()->user();

        $sentMessages = $authUser->sentMessages()->where('receiver_id', $userId)->get();

        $receivedMessages = $authUser->receivedMessages()->where('sender_id', $userId)->get();

        $messages = $sentMessages->merge($receivedMessages)->sortBy('created_at')->values();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Messages retrieved successfully',
            'data' => $messages
        ], 200);
    }
        
}
