<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getConversations()
    {
        $user = Auth::user();

        // Get conversations for the user
        $conversations = $user->conversations()
            ->with(['seller', 'latestMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // If user is also a seller, get conversations where they are the seller
        if ($user->seller) {
            $sellerConversations = $user->seller->conversations()
                ->with(['user', 'latestMessage'])
                ->orderBy('updated_at', 'desc')
                ->get();

            $conversations = $conversations->merge($sellerConversations)->unique('id');
        }

        return response()->json($conversations);
    }

    public function startOrGetConversation(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id'
        ]);

        $user = Auth::user();
        $sellerId = $request->seller_id;

        // Check if conversation already exists
        $conversation = Conversation::where('user_id', $user->id)
            ->where('seller_id', $sellerId)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'seller_id' => $sellerId
            ]);
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'seller' => $conversation->seller
        ]);
    }

    public function getMessages(Request $request, $conversationId)
    {
        $user = Auth::user();

        // Verify user has access to this conversation
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id);

                if ($user->seller) {
                    $query->orWhere('seller_id', $user->seller->id);
                }
            })
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sender_type' => $message->sender_type,
                    'sender_name' => $message->sender_name,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'is_own' => $this->isOwnMessage($message)
                ];
            });

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        $conversationId = $request->conversation_id;

        // Verify user has access to this conversation
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id);

                if ($user->seller) {
                    $query->orWhere('seller_id', $user->seller->id);
                }
            })
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // Determine sender type and ID
        $senderType = 'user';
        $senderId = $user->id;

        if ($user->seller && $conversation->seller_id == $user->seller->id) {
            $senderType = 'seller';
            $senderId = $user->seller->id;
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'content' => $request->content
        ]);

        // Update conversation timestamp
        $conversation->touch();

        return response()->json([
            'id' => $message->id,
            'content' => $message->content,
            'sender_type' => $message->sender_type,
            'sender_name' => $message->sender_name,
            'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            'is_own' => true
        ]);
    }

    public function getSellers()
    {
        $sellers = Seller::where('approved', true)
            ->with('user')
            ->get()
            ->map(function($seller) {
                return [
                    'id' => $seller->id,
                    'business_name' => $seller->business_name,
                    'logo_url' => $seller->profile_image_url
                ];
            });

        return response()->json($sellers);
    }

    private function isOwnMessage($message)
    {
        $user = Auth::user();

        if ($message->sender_type === 'user' && $message->sender_id === $user->id) {
            return true;
        }

        if ($message->sender_type === 'seller' && $user->seller && $message->sender_id === $user->seller->id) {
            return true;
        }

        return false;
    }
}
