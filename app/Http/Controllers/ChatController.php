<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\Order;
use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;

class ChatController extends Controller
{
    /**
     * Open Chat Conversation
     */
    public function show($conversationId)
    {
        $user = Auth::user();

        $conversation = ChatConversation::with([
            'buyer.user',
            'seller.user',
            'order',
            'project'
        ])->findOrFail($conversationId);

        /*
    |--------------------------------------------------------------------------
    | Security Check
    |--------------------------------------------------------------------------
    */

        $buyerUserId = optional($conversation->buyer)->id;

        $sellerUserId = optional($conversation->seller)->id;

        if (
            $buyerUserId != $user->id &&
            $sellerUserId != $user->id
        ) {
            abort(403);
        }

        return response()->json([
            'status' => true,
            'conversation' => $conversation
        ]);
    }

    /**
     * Load Conversation Messages
     */
    public function loadMessages($conversationId)
    {
        $user = Auth::user();
        $conversation = ChatConversation::findOrFail($conversationId);
        $buyerUserId = optional($conversation->buyer)->id;
        $sellerUserId = optional($conversation->seller)->id;

        if (
            $buyerUserId != $user->id &&
            $sellerUserId != $user->id
        ) {
            abort(403);
        }

        $messages = ChatMessage::with(['sender.buyer', 'sender.seller'])
            ->where('conversation_id', $conversation->id)
            ->orderBy('id')
            ->get()->map(function ($message) {

                $message->chat_time = $message->created_at->format('h:i A');

                return $message;
            });

        return response()->json([
            'status' => true,
            'messages' => $messages
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
            [
                'project_id' => $project->id,
            ],
            [
                'buyer_id'  => $buyer->user_id,
                'seller_id' => $seller->user_id,
                'order_id'  => $project->order_id,
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

    /**
     * Send Message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([

            'conversation_id' => 'required|exists:chat_conversations,id',

            'message' => 'nullable|string',

            'attachment' => 'nullable|file|max:20480',

        ]);

        $user = Auth::user();

        $conversation = ChatConversation::findOrFail(
            $request->conversation_id
        );

        $buyerUserId = optional($conversation->buyer)->id;

        $sellerUserId = optional($conversation->seller)->id;

        if (
            $buyerUserId != $user->id &&
            $sellerUserId != $user->id
        ) {
            abort(403);
        }

        $attachment = null;

        if ($request->hasFile('attachment')) {

            $attachment = $request->file('attachment')
                ->store('chat', 'public');
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message'         => $request->message,
            'attachment'      => $attachment,
            'message_type'    => $attachment ? 'file' : 'text',
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        if ($buyerUserId == $user->id) {

            $receiverId = $sellerUserId;
        } else {

            $receiverId = $buyerUserId;
        }

        $senderName = optional($user->buyer)->full_name ?? optional($user->seller)->full_name ?? $user->username;

        NotificationService::send(
            $receiverId,
            'New Message',
            $senderName . ' sent you a new message.',
            route(auth()->user()->role == 'buyer' ? 'seller.chat.show' : 'buyer.chat.show', $conversation->id),
            'message'
        );

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ]);
    }

    /**
     * Mark Messages as Seen
     */
    public function markAsSeen($conversationId)
    {
        $user = Auth::user();

        ChatMessage::where('conversation_id', $conversationId)
            ->whereNull('seen_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['seen_at' => now(),]);

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * Delete Message
     */
    public function deleteMessage($id)
    {
        $message = ChatMessage::findOrFail($id);

        if ($message->sender_id != Auth::id()) {
            abort(403);
        }

        $message->delete();

        return response()->json([
            'status' => true,
            'message' => 'Message deleted successfully.'
        ]);
    }
}
