<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPresence;
use App\Models\VideoCallQueue;
use App\Models\VideoCallSession;
use App\Services\LiveKitVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\View\View;

class SpeakingVideoCallController extends Controller
{
    public function __construct(
        protected LiveKitVideoService $liveKitVideoService
    ) {
    }

    public function index(Request $request): View
    {
        $this->touchPresence($request->user(), true, $request->path());

        return view('speaking.video-call', [
            'livekitConfigured' => $this->liveKitVideoService->isConfigured(),
            'videoCallStatusEndpoint' => route('speaking.video-call.status'),
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->touchPresence($user, true, $request->path());
        $this->cleanupStaleState();

        $session = $this->findCurrentSession($user->id);
        $queue = VideoCallQueue::query()
            ->where('user_id', $user->id)
            ->where('status', 'searching')
            ->first();

        return response()->json([
            'provider' => 'livekit',
            'configured' => $this->liveKitVideoService->isConfigured(),
            'is_searching' => (bool) $queue,
            'queue_started_at' => optional($queue?->requested_at)->toIso8601String(),
            'current_session' => $session ? $this->serializeSession($session, $user) : null,
            'online_friends' => $this->onlineFriends($user),
        ]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'path' => ['sometimes', 'nullable', 'string', 'max:255'],
            'video_ready' => ['sometimes', 'boolean'],
        ]);

        $this->touchPresence(
            $request->user(),
            (bool) ($payload['video_ready'] ?? false),
            (string) ($payload['path'] ?? $request->path())
        );

        return response()->json(['ok' => true]);
    }

    public function joinRandomQueue(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->touchPresence($user, true, $request->path());
        $this->cleanupStaleState();

        if (! $this->liveKitVideoService->isConfigured()) {
            return response()->json([
                'message' => 'LiveKit video call is not configured yet.',
            ], 503);
        }

        if ($this->findCurrentSession($user->id)) {
            return response()->json([
                'message' => 'Finish your current call before starting a new match.',
            ], 422);
        }

        try {
            $matchedSession = DB::transaction(function () use ($user) {
                $existingQueue = VideoCallQueue::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($existingQueue && $existingQueue->status === 'searching') {
                    $existingQueue->update([
                        'last_seen_at' => now(),
                    ]);
                } else {
                    VideoCallQueue::query()->updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'status' => 'searching',
                            'requested_at' => now(),
                            'matched_at' => null,
                            'last_seen_at' => now(),
                        ]
                    );
                }

                $candidate = VideoCallQueue::query()
                    ->where('user_id', '!=', $user->id)
                    ->where('status', 'searching')
                    ->where('last_seen_at', '>=', now()->subMinutes(2))
                    ->orderBy('requested_at')
                    ->lockForUpdate()
                    ->get()
                    ->first(function (VideoCallQueue $queue) {
                        if ($this->hasOpenSession((int) $queue->user_id)) {
                            $queue->delete();

                            return false;
                        }

                        return true;
                    });

                if (! $candidate) {
                    return null;
                }

                $session = VideoCallSession::query()->create([
                    'mode' => 'random',
                    'status' => 'active',
                    'host_user_id' => $candidate->user_id,
                    'guest_user_id' => $user->id,
                    'created_by' => $user->id,
                    'daily_room_name' => $this->liveKitVideoService->generateRoomName('random'),
                    'daily_room_url' => null,
                    'daily_payload' => [
                        'provider' => 'livekit',
                    ],
                    'room_expires_at' => now()->addSeconds($this->liveKitVideoService->tokenTtlSeconds()),
                    'accepted_at' => now(),
                    'started_at' => now(),
                    'last_activity_at' => now(),
                ]);

                VideoCallQueue::query()
                    ->whereIn('user_id', [$candidate->user_id, $user->id])
                    ->delete();

                return $session;
            });
        } catch (Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => $matchedSession
                ? 'Match found. Joining your LiveKit room now.'
                : 'You are now in the random matching queue.',
            'matched' => (bool) $matchedSession,
            'session' => $matchedSession ? $this->serializeSession($matchedSession, $user) : null,
        ]);
    }

    public function cancelRandomQueue(Request $request): JsonResponse
    {
        VideoCallQueue::query()
            ->where('user_id', $request->user()->id)
            ->delete();

        $this->touchPresence($request->user(), true, $request->path());

        return response()->json([
            'message' => 'Random matching cancelled.',
        ]);
    }

    public function inviteFriend(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();
        $this->touchPresence($currentUser, true, $request->path());
        $this->cleanupStaleState();

        if (! $this->liveKitVideoService->isConfigured()) {
            return response()->json([
                'message' => 'LiveKit video call is not configured yet.',
            ], 503);
        }

        if ($currentUser->id === $user->id) {
            return response()->json([
                'message' => 'You cannot call yourself.',
            ], 422);
        }

        if (! $currentUser->isFriendsWith($user)) {
            return response()->json([
                'message' => 'You can only start video calls with friends.',
            ], 403);
        }

        if ($this->findCurrentSession($currentUser->id)) {
            return response()->json([
                'message' => 'Finish your current call before inviting a friend.',
            ], 422);
        }

        VideoCallQueue::query()
            ->whereIn('user_id', [$currentUser->id, $user->id])
            ->delete();

        $recipientPresence = UserPresence::query()
            ->where('user_id', $user->id)
            ->recentlyOnline()
            ->where('is_video_available', true)
            ->first();

        if (! $recipientPresence) {
            return response()->json([
                'message' => 'This friend is not available in the video call room right now.',
            ], 422);
        }

        if ($this->hasOpenSession($user->id)) {
            return response()->json([
                'message' => 'This friend is already busy in another call.',
            ], 422);
        }

        $existing = VideoCallSession::query()
            ->whereIn('status', ['ringing', 'active'])
            ->where(function ($query) use ($currentUser, $user) {
                $query
                    ->where(function ($inner) use ($currentUser, $user) {
                        $inner->where('host_user_id', $currentUser->id)
                            ->where('guest_user_id', $user->id);
                    })
                    ->orWhere(function ($inner) use ($currentUser, $user) {
                        $inner->where('host_user_id', $user->id)
                            ->where('guest_user_id', $currentUser->id);
                    });
            })
            ->latest('id')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'A video call session already exists for you and this friend.',
                'session' => $this->serializeSession($existing, $currentUser),
            ], 200);
        }

        $session = VideoCallSession::query()->create([
            'mode' => 'friend',
            'status' => 'ringing',
            'host_user_id' => $currentUser->id,
            'guest_user_id' => $user->id,
            'created_by' => $currentUser->id,
            'daily_room_name' => $this->liveKitVideoService->generateRoomName('friend'),
            'daily_room_url' => null,
            'daily_payload' => [
                'provider' => 'livekit',
            ],
            'room_expires_at' => now()->addSeconds($this->liveKitVideoService->tokenTtlSeconds()),
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'message' => 'Friend video call invitation sent.',
            'session' => $this->serializeSession($session, $currentUser),
        ]);
    }

    public function accept(Request $request, VideoCallSession $videoCallSession): JsonResponse
    {
        $user = $request->user();

        if ((int) $videoCallSession->guest_user_id !== (int) $user->id) {
            return response()->json([
                'message' => 'You are not allowed to accept this call.',
            ], 403);
        }

        if ($videoCallSession->status !== 'ringing') {
            return response()->json([
                'message' => 'This call is no longer waiting for acceptance.',
            ], 422);
        }

        $videoCallSession->update([
            'status' => 'active',
            'accepted_at' => now(),
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);

        $this->touchPresence($user, true, $request->path());

        return response()->json([
            'message' => 'Call accepted.',
            'session' => $this->serializeSession($videoCallSession->fresh(['host', 'guest']), $user),
        ]);
    }

    public function decline(Request $request, VideoCallSession $videoCallSession): JsonResponse
    {
        $user = $request->user();

        if (! $videoCallSession->involvesUser($user->id)) {
            return response()->json([
                'message' => 'You are not allowed to update this call.',
            ], 403);
        }

        if (! in_array($videoCallSession->status, ['ringing', 'active'], true)) {
            return response()->json([
                'message' => 'This call has already been closed.',
            ], 422);
        }

        $videoCallSession->update([
            'status' => $videoCallSession->status === 'ringing' ? 'declined' : 'ended',
            'declined_at' => $videoCallSession->status === 'ringing' ? now() : $videoCallSession->declined_at,
            'ended_at' => $videoCallSession->status === 'active' ? now() : $videoCallSession->ended_at,
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'message' => $videoCallSession->status === 'declined' ? 'Call declined.' : 'Call ended.',
        ]);
    }

    public function leave(Request $request, VideoCallSession $videoCallSession): JsonResponse
    {
        $user = $request->user();

        if (! $videoCallSession->involvesUser($user->id)) {
            return response()->json([
                'message' => 'You are not allowed to leave this call.',
            ], 403);
        }

        $nextStatus = $videoCallSession->status === 'ringing' ? 'cancelled' : 'ended';

        $videoCallSession->update([
            'status' => $nextStatus,
            'ended_at' => now(),
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'message' => $nextStatus === 'cancelled' ? 'Invitation cancelled.' : 'Call ended.',
        ]);
    }

    protected function cleanupStaleState(): void
    {
        VideoCallQueue::query()
            ->where('last_seen_at', '<', now()->subMinutes(2))
            ->delete();

        VideoCallSession::query()
            ->where('status', 'ringing')
            ->where('created_at', '<', now()->subMinutes(3))
            ->update([
                'status' => 'cancelled',
                'ended_at' => now(),
                'last_activity_at' => now(),
            ]);
    }

    protected function findCurrentSession(int $userId): ?VideoCallSession
    {
        return VideoCallSession::query()
            ->with(['host:id,name,email', 'guest:id,name,email'])
            ->where(function ($query) use ($userId) {
                $query->where('host_user_id', $userId)
                    ->orWhere('guest_user_id', $userId);
            })
            ->whereIn('status', ['ringing', 'active'])
            ->latest('id')
            ->first();
    }

    protected function onlineFriends(User $user): array
    {
        $friendIds = $user->friendIds();

        if ($friendIds->isEmpty()) {
            return [];
        }

        $activeParticipantIds = VideoCallSession::query()
            ->where('status', 'active')
            ->where(function ($query) use ($friendIds) {
                $query->whereIn('host_user_id', $friendIds)
                    ->orWhereIn('guest_user_id', $friendIds);
            })
            ->get()
            ->flatMap(fn (VideoCallSession $session) => [$session->host_user_id, $session->guest_user_id])
            ->unique()
            ->flip();

        return UserPresence::query()
            ->with('user:id,name,email')
            ->whereIn('user_id', $friendIds)
            ->recentlyOnline()
            ->where('is_video_available', true)
            ->orderByDesc('last_seen_at')
            ->get()
            ->map(function (UserPresence $presence) use ($activeParticipantIds) {
                $friend = $presence->user;

                return [
                    'id' => $friend?->id,
                    'name' => $friend?->name,
                    'email' => $friend?->email,
                    'avatar' => mb_strtoupper(trim(mb_substr((string) ($friend?->name ?? 'U'), 0, 2))),
                    'last_seen_label' => optional($presence->last_seen_at)->diffForHumans(),
                    'is_busy' => $friend && $activeParticipantIds->has($friend->id),
                ];
            })
            ->filter(fn (array $friend) => ! empty($friend['id']))
            ->values()
            ->all();
    }

    protected function touchPresence(User $user, bool $videoReady = false, ?string $path = null): void
    {
        UserPresence::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'current_path' => $path,
                'is_video_available' => $videoReady,
                'last_seen_at' => now(),
            ]
        );
    }

    protected function hasOpenSession(int $userId): bool
    {
        return VideoCallSession::query()
            ->where(function ($query) use ($userId) {
                $query->where('host_user_id', $userId)
                    ->orWhere('guest_user_id', $userId);
            })
            ->whereIn('status', ['ringing', 'active'])
            ->exists();
    }

    protected function serializeSession(VideoCallSession $session, User $currentUser): array
    {
        $currentUserId = (int) $currentUser->id;
        $otherUser = (int) $session->host_user_id === $currentUserId ? $session->guest : $session->host;

        $connection = null;
        if ($session->status === 'active' && $this->liveKitVideoService->isConfigured()) {
            $connection = $this->liveKitVideoService->buildConnectionPayload($session, $currentUser);
        }

        return [
            'id' => $session->id,
            'mode' => $session->mode,
            'status' => $session->status,
            'is_host' => (int) $session->host_user_id === $currentUserId,
            'is_incoming' => $session->status === 'ringing' && (int) $session->guest_user_id === $currentUserId,
            'room_name' => $session->daily_room_name,
            'room_expires_at' => optional($session->room_expires_at)->toIso8601String(),
            'created_at_label' => optional($session->created_at)->diffForHumans(),
            'started_at_label' => optional($session->started_at)->diffForHumans(),
            'connection' => $connection,
            'other_participant' => [
                'id' => $otherUser?->id,
                'name' => $otherUser?->name,
                'email' => $otherUser?->email,
                'avatar' => mb_strtoupper(trim(mb_substr((string) ($otherUser?->name ?? 'U'), 0, 2))),
            ],
        ];
    }
}
