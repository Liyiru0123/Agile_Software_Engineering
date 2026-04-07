<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FriendController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $friendIds = $user->friendIds();

        $friends = User::query()
            ->whereIn('id', $friendIds)
            ->orderBy('name')
            ->get();

        $incomingRequests = FriendRequest::query()
            ->with('sender:id,name,email')
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $outgoingRequests = FriendRequest::query()
            ->with('receiver:id,name,email')
            ->where('sender_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $conversationMap = Conversation::query()
            ->where('type', 'direct')
            ->whereIn('direct_key', $friendIds->map(fn (int $friendId) => Conversation::directKeyFor($user->id, $friendId)))
            ->pluck('id', 'direct_key');

        return view('social.friends', [
            'friends' => $friends,
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests,
            'conversationMap' => $conversationMap,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $payload = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['sometimes', 'nullable', 'string', 'max:240'],
            'source_type' => ['sometimes', 'nullable', 'string', 'in:forum_post,forum_comment,profile'],
            'source_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ]);

        $sender = $request->user();
        $receiver = User::query()->findOrFail($payload['receiver_id']);

        if ($sender->id === $receiver->id) {
            return $this->respond($request, 'You cannot send a friend request to yourself.', false, 422);
        }

        if ($sender->isFriendsWith($receiver)) {
            return $this->respond($request, 'You are already friends.', true);
        }

        $existingOutgoing = FriendRequest::query()
            ->where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->where('status', 'pending')
            ->first();

        if ($existingOutgoing) {
            return $this->respond($request, 'A friend request is already pending.', true);
        }

        $existingIncoming = FriendRequest::query()
            ->where('sender_id', $receiver->id)
            ->where('receiver_id', $sender->id)
            ->where('status', 'pending')
            ->first();

        if ($existingIncoming) {
            $this->acceptRequest($existingIncoming, $sender, $receiver);

            return $this->respond($request, 'Friend request accepted. You are now friends.', true);
        }

        FriendRequest::query()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $payload['message'] ?? null,
            'source_type' => $payload['source_type'] ?? null,
            'source_id' => $payload['source_id'] ?? null,
            'status' => 'pending',
        ]);

        return $this->respond($request, 'Friend request sent.', true);
    }

    public function accept(Request $request, FriendRequest $friendRequest): RedirectResponse|JsonResponse
    {
        if ((int) $friendRequest->receiver_id !== (int) $request->user()->id || $friendRequest->status !== 'pending') {
            abort(403);
        }

        $this->acceptRequest($friendRequest, $friendRequest->receiver, $friendRequest->sender);

        return $this->respond($request, 'Friend request accepted.', true);
    }

    public function reject(Request $request, FriendRequest $friendRequest): RedirectResponse|JsonResponse
    {
        if ((int) $friendRequest->receiver_id !== (int) $request->user()->id || $friendRequest->status !== 'pending') {
            abort(403);
        }

        $friendRequest->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        return $this->respond($request, 'Friend request rejected.', true);
    }

    public function cancel(Request $request, FriendRequest $friendRequest): RedirectResponse|JsonResponse
    {
        if ((int) $friendRequest->sender_id !== (int) $request->user()->id || $friendRequest->status !== 'pending') {
            abort(403);
        }

        $friendRequest->update([
            'status' => 'cancelled',
            'responded_at' => now(),
        ]);

        return $this->respond($request, 'Friend request cancelled.', true);
    }

    public function destroy(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            return $this->respond($request, 'You cannot remove yourself.', false, 422);
        }

        [$userOneId, $userTwoId] = Friendship::orderedPair($currentUser->id, $user->id);

        $deleted = Friendship::query()
            ->where('user_one_id', $userOneId)
            ->where('user_two_id', $userTwoId)
            ->delete();

        if (! $deleted) {
            return $this->respond($request, 'This user is not in your friend list.', false, 404);
        }

        return $this->respond($request, 'Friend removed.', true);
    }

    protected function acceptRequest(FriendRequest $friendRequest, User $receiver, User $sender): void
    {
        DB::transaction(function () use ($friendRequest, $receiver, $sender) {
            $friendRequest->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            [$userOneId, $userTwoId] = Friendship::orderedPair($receiver->id, $sender->id);

            Friendship::query()->firstOrCreate([
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
            ]);

            Conversation::firstOrCreateDirectBetween($receiver, $sender);
        });
    }

    protected function respond(Request $request, string $message, bool $success, int $status = 200): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ], $status);
        }

        return back()->with('social_status', $message);
    }
}
