<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request, ?Conversation $conversation = null): View
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->with(['participants:id,name', 'latestMessage.sender:id,name'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        $selectedConversation = $conversation;

        if (! $selectedConversation && $conversations->isNotEmpty()) {
            $selectedConversation = $conversations->first();
        }

        $messages = collect();
        $selectedFriend = null;

        if ($selectedConversation) {
            abort_unless($selectedConversation->hasParticipant($user->id), 403);

            $selectedConversation->load([
                'participants:id,name',
                'messages' => fn ($query) => $query
                    ->with(['sender:id,name', 'replyParent.sender:id,name'])
                    ->latest()
                    ->take(80),
            ]);

            $messages = $selectedConversation->messages->sortBy('created_at')->values();
            $selectedFriend = $selectedConversation->otherParticipant($user->id);

            $selectedConversation->participants()->updateExistingPivot($user->id, [
                'last_read_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $conversationMeta = $conversations->map(function (Conversation $item) use ($user) {
            $other = $item->otherParticipant($user->id);
            $lastReadAt = optional($item->participants->firstWhere('id', $user->id)?->pivot)->last_read_at;
            $hasUnread = false;

            if ($item->latestMessage && $item->latestMessage->sender_id !== $user->id) {
                $hasUnread = ! $lastReadAt || $item->latestMessage->created_at?->gt($lastReadAt);
            }

            return [
                'conversation' => $item,
                'other' => $other,
                'has_unread' => $hasUnread,
            ];
        });

        return view('social.messages', [
            'conversations' => $conversationMeta,
            'selectedConversation' => $selectedConversation,
            'selectedFriend' => $selectedFriend,
            'messages' => $messages,
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = $request->user();
        $recipient = User::query()->findOrFail($payload['recipient_id']);

        if ($user->id === $recipient->id) {
            return back()->with('social_status', 'You cannot message yourself.');
        }

        if (! $user->isFriendsWith($recipient)) {
            return back()->with('social_status', 'You can only send direct messages to friends.');
        }

        $conversation = Conversation::firstOrCreateDirectBetween($user, $recipient);

        return redirect()
            ->route('messages.show', $conversation)
            ->with('social_status', 'Conversation opened.');
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = $request->user();
        abort_unless($conversation->hasParticipant($user->id), 403);

        $payload = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $recipient = $conversation->otherParticipant($user->id);
        abort_unless($recipient && $user->isFriendsWith($recipient), 403);

        DB::transaction(function () use ($conversation, $user, $payload, $recipient) {
            ConversationMessage::query()->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'body' => $payload['body'],
            ]);

            $conversation->forceFill([
                'last_message_at' => now(),
            ])->save();

            $conversation->participants()->updateExistingPivot($user->id, [
                'last_read_at' => now(),
                'updated_at' => now(),
            ]);

            $conversation->participants()->updateExistingPivot($recipient->id, [
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('messages.show', $conversation)
            ->with('social_status', 'Message sent.');
    }
}
