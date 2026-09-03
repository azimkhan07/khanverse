<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\Project;
use App\Services\NotificationService;

class ChatApiController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $conversations = ChatConversation::with(['buyer', 'seller', 'order', 'project', 'latestMessage'])
            ->where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->latest('last_message_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                $otherUserId = $conversation->buyer_id === $userId ? $conversation->seller_id : $conversation->buyer_id;
                $other = \App\Models\User::where('id', $otherUserId)->first();

                return [
                    'id' => $conversation->id,
                    'buyer_id' => $conversation->buyer_id,
                    'seller_id' => $conversation->seller_id,
                    'order_id' => $conversation->order_id,
                    'project_id' => $conversation->project_id,
                    'last_message_at' => $conversation->last_message_at,
                    'other_user' => [
                        'id' => $other?->id,
                        'name' => $other?->display_name,
                        'image' => $other?->display_image,
                    ],
                    'last_message' => $conversation->latestMessage ? [
                        'id' => $conversation->latestMessage->id,
                        'sender_id' => $conversation->latestMessage->sender_id,
                        'message' => $conversation->latestMessage->message,
                        'attachment' => $conversation->latestMessage->attachment,
                        'created_at' => $conversation->latestMessage->created_at,
                    ] : null,
                ];
            });

        return response()->json([
            'status' => true,
            'conversations' => $conversations,
        ]);
    }

    public function show($conversationId)
    {
        $user = Auth::user();
        $conversation = ChatConversation::with(['buyer.user', 'seller.user', 'order', 'project'])
            ->findOrFail($conversationId);

        $this->authorizeParticipant($conversation, $user->id);

        return response()->json([
            'status' => true,
            'conversation' => $conversation,
        ]);
    }

    public function loadMessages($conversationId)
    {
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);
        $this->authorizeParticipant($conversation, $user->id);

        $userId = Auth::id();

        $messages = ChatMessage::with(['sender.buyer', 'sender.seller'])
            ->where('conversation_id', $conversation->id)
            ->orderBy('id')
            ->get()
            ->map(function ($message) use ($userId) {
                $message->chat_time = $message->created_at->format('h:i A');
                $message->is_mine = $message->sender_id === $userId;
                return $message;
            });

        return response()->json([
            'status' => true,
            'messages' => $messages,
        ]);
    }

    public function openConversation(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($request->project_id);
        $buyer = Buyer::findOrFail($project->buyer_id);
        $seller = Seller::findOrFail($project->seller_id);

        $conversation = ChatConversation::firstOrCreate(
            ['project_id' => $project->id],
            [
                'buyer_id' => $buyer->user_id,
                'seller_id' => $seller->user_id,
                'order_id' => $project->order_id,
            ]
        );

        return response()->json([
            'status' => true,
            'conversation_id' => $conversation->id,
            'buyer' => [
                'id' => $buyer->user_id,
                'name' => $buyer->full_name,
                'image' => $buyer->profile_image,
            ],
            'seller' => [
                'id' => $seller->user_id,
                'name' => $seller->full_name,
                'image' => $seller->profile_image,
            ],
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:20480',
        ]);

        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($request->conversation_id);
        $this->authorizeParticipant($conversation, $user->id);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('chat', 'public');
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'attachment' => $attachment ? asset('storage/' . $attachment) : null,
            'message_type' => $attachment ? 'file' : 'text',
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($conversation->buyer_id == $user->id) {
            $receiverId = $conversation->seller_id;
        } else {
            $receiverId = $conversation->buyer_id;
        }

        $senderName = optional($user->buyer)->full_name ?? optional($user->seller)->full_name ?? $user->username;

        $receiver = \App\Models\User::with('seller', 'buyer')->find($receiverId);
        $receiverRole = $receiver->role ?? 'buyer';
        $projectUrl = $receiverRole === 'seller'
            ? url('/seller/projects/' . $conversation->project_id)
            : url('/buyer/projects/' . $conversation->project_id);

        NotificationService::send(
            $receiverId,
            'New Message',
            $senderName . ' sent you a new message.',
            'message',
            $projectUrl
        );

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ]);
    }

    public function markAsSeen($conversationId)
    {
        $user = Auth::user();

        ChatMessage::where('conversation_id', $conversationId)
            ->whereNull('seen_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['seen_at' => now()]);

        return response()->json(['status' => true]);
    }

    public function deleteMessage($id)
    {
        $message = ChatMessage::findOrFail($id);
        if ($message->sender_id != Auth::id()) {
            abort(403);
        }
        $message->delete();

        return response()->json([
            'status' => true,
            'message' => 'Message deleted successfully.',
        ]);
    }

    protected function authorizeParticipant(ChatConversation $conversation, int $userId): void
    {
        if ($conversation->buyer_id != $userId && $conversation->seller_id != $userId) {
            abort(403);
        }
    }
}
