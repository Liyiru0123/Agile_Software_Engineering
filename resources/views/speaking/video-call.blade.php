@extends('layouts.app')

@section('title', 'Video Call')
@section('video_presence', '1')

@push('styles')
<style>
    .livekit-media-slot video,
    .livekit-media-slot audio {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #2c1810;
        border-radius: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-7xl px-6">
        <a href="{{ route('speaking.hub') }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">Back to Speaking Hub</a>

        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Speaking</div>
                <h1 class="mt-2 text-4xl font-black tracking-tight text-[#4A2C2A]">Video Call Practice Room</h1>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-[#6B3D2E]">
                    Match with another learner for a random speaking call or invite an online friend who is already available in the room.
                </p>
            </div>
            <div class="rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#6B3D2E]">
                LiveKit powered
            </div>
        </div>

        <div id="video-call-alert" class="hidden mb-6 rounded-2xl px-4 py-3 text-sm"></div>

        @if(! $livekitConfigured)
            <div class="mb-6 rounded-[1.75rem] border border-amber-200 bg-amber-50 px-6 py-5 text-sm leading-7 text-amber-800">
                LiveKit is not configured yet. Add <code>LIVEKIT_URL</code>, <code>LIVEKIT_API_KEY</code>, and <code>LIVEKIT_API_SECRET</code> in your <code>.env</code> before using this page.
            </div>
        @endif

        <div class="grid gap-8 xl:grid-cols-[minmax(0,1.25fr)_360px]">
            <section class="space-y-6">
                <article class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Random Match</div>
                            <h2 class="mt-2 text-2xl font-black text-[#4A2C2A]">Start a quick speaking call</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-[#6B3D2E]">
                                Join the queue to be paired with another user who is also waiting in the speaking video room.
                            </p>
                        </div>
                        <button id="random-match-button"
                                class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E] disabled:cursor-not-allowed disabled:opacity-60"
                                @disabled(! $livekitConfigured)>
                            Find a random partner
                        </button>
                    </div>

                    <div id="queue-status-card" class="mt-6 rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] px-5 py-5 text-sm leading-7 text-[#6B3D2E]">
                        You are not in the queue right now.
                    </div>
                </article>

                <article class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Current Call</div>
                            <h2 class="mt-2 text-2xl font-black text-[#4A2C2A]">Room status</h2>
                        </div>
                        <div id="session-badges" class="flex flex-wrap items-center gap-2"></div>
                    </div>

                    <div id="incoming-call-card" class="hidden mt-6 rounded-[1.5rem] border border-[#D4B970] bg-[#FFF8EE] p-5">
                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Incoming friend call</div>
                        <div id="incoming-call-title" class="mt-2 text-lg font-black text-[#4A2C2A]"></div>
                        <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">Accept to join the LiveKit room, or decline to stay available for other practice.</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            <button id="accept-call-button" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                                Accept Call
                            </button>
                            <button id="decline-call-button" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:bg-[#F3E7D8]">
                                Decline
                            </button>
                        </div>
                    </div>

                    <div id="session-summary-card" class="hidden mt-6 rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] p-5">
                        <div class="flex flex-wrap items-center gap-4">
                            <span id="session-avatar" class="flex h-14 w-14 items-center justify-center rounded-full bg-[#D4B970] text-base font-black uppercase tracking-[0.08em] text-[#4A2C2A]"></span>
                            <div class="min-w-0 flex-1">
                                <div id="session-partner-name" class="text-lg font-black text-[#4A2C2A]"></div>
                                <div id="session-meta" class="mt-1 text-sm text-[#8B6B47]"></div>
                            </div>
                            <button id="leave-call-button" class="hidden rounded-2xl border border-red-200 px-5 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                Leave Call
                            </button>
                        </div>
                    </div>

                    <div id="call-placeholder" class="mt-6 rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-[#FBF7F1] px-6 py-16 text-center text-[#8B6B47]">
                        No active call yet. Start a random match or invite an online friend.
                    </div>

                    <div id="livekit-room-shell" class="hidden mt-6 space-y-4">
                        <div class="rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] px-5 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Live Room</div>
                                    <div id="livekit-room-meta" class="mt-2 text-sm leading-6 text-[#6B3D2E]">Preparing your room connection...</div>
                                </div>
                                <div id="livekit-connection-status" class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#6B3D2E]">
                                    Waiting
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-2">
                            <article class="rounded-[1.5rem] border border-[#E6D3BC] bg-[#2C1810] p-4 text-white">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold text-[#F5E6D3]">You</div>
                                    <div id="local-media-status" class="text-xs uppercase tracking-[0.14em] text-[#D7BE8A]">Waiting for camera</div>
                                </div>
                                <div id="local-video-slot" class="livekit-media-slot mt-4 flex h-[320px] items-center justify-center overflow-hidden rounded-[1.25rem] bg-[#4A2C2A] text-sm text-[#D7BE8A]">
                                    Camera preview will appear here.
                                </div>
                            </article>

                            <article class="rounded-[1.5rem] border border-[#E6D3BC] bg-[#2C1810] p-4 text-white">
                                <div class="flex items-center justify-between gap-3">
                                    <div id="remote-participant-name" class="text-sm font-semibold text-[#F5E6D3]">Partner</div>
                                    <div id="remote-media-status" class="text-xs uppercase tracking-[0.14em] text-[#D7BE8A]">Waiting to join</div>
                                </div>
                                <div id="remote-video-slot" class="livekit-media-slot mt-4 flex h-[320px] items-center justify-center overflow-hidden rounded-[1.25rem] bg-[#4A2C2A] text-sm text-[#D7BE8A]">
                                    Your speaking partner will appear here.
                                </div>
                            </article>
                        </div>

                        <div id="remote-audio-slot" class="hidden"></div>
                    </div>
                </article>
            </section>

            <aside class="space-y-6">
                <article class="rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Friends</div>
                            <h2 class="mt-2 text-2xl font-black text-[#4A2C2A]">Online now</h2>
                        </div>
                        <span id="online-friend-count" class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">0</span>
                    </div>

                    <p class="mt-3 text-sm leading-7 text-[#6B3D2E]">
                        Only friends who are currently available in the video room appear here, so they can receive your call in real time.
                    </p>

                    <div id="friend-list" class="mt-6 space-y-4"></div>
                </article>

                <article class="rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">How it works</div>
                    <div class="mt-4 space-y-4 text-sm leading-7 text-[#6B3D2E]">
                        <p>Keep this page open to stay available for friend calls.</p>
                        <p>Random matching pairs two learners and drops both into the same LiveKit room.</p>
                        <p>Friend calls keep the room private for just the two participants.</p>
                    </div>
                </article>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>
<script>
(() => {
    const endpoints = {
        status: @json(route('speaking.video-call.status')),
        heartbeat: @json(route('speaking.video-call.heartbeat')),
        random: @json(route('speaking.video-call.random')),
        cancelRandom: @json(route('speaking.video-call.random.cancel')),
        inviteFriendBase: @json(url('/speaking/video-call/friends')),
        acceptBase: @json(url('/speaking/video-call/sessions')),
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const configured = @json($livekitConfigured);

    const alertEl = document.getElementById('video-call-alert');
    const randomButton = document.getElementById('random-match-button');
    const queueCard = document.getElementById('queue-status-card');
    const incomingCard = document.getElementById('incoming-call-card');
    const incomingTitle = document.getElementById('incoming-call-title');
    const acceptCallButton = document.getElementById('accept-call-button');
    const declineCallButton = document.getElementById('decline-call-button');
    const leaveCallButton = document.getElementById('leave-call-button');
    const sessionSummaryCard = document.getElementById('session-summary-card');
    const sessionBadges = document.getElementById('session-badges');
    const sessionAvatar = document.getElementById('session-avatar');
    const sessionPartnerName = document.getElementById('session-partner-name');
    const sessionMeta = document.getElementById('session-meta');
    const placeholder = document.getElementById('call-placeholder');
    const roomShell = document.getElementById('livekit-room-shell');
    const roomMeta = document.getElementById('livekit-room-meta');
    const roomConnectionStatus = document.getElementById('livekit-connection-status');
    const localVideoSlot = document.getElementById('local-video-slot');
    const remoteVideoSlot = document.getElementById('remote-video-slot');
    const remoteAudioSlot = document.getElementById('remote-audio-slot');
    const localMediaStatus = document.getElementById('local-media-status');
    const remoteMediaStatus = document.getElementById('remote-media-status');
    const remoteParticipantName = document.getElementById('remote-participant-name');
    const friendList = document.getElementById('friend-list');
    const onlineFriendCount = document.getElementById('online-friend-count');

    const state = {
        session: null,
        searching: false,
        room: null,
        connectedSessionId: null,
        pollTimer: null,
        heartbeatTimer: null,
    };

    function showAlert(message, type = 'info') {
        if (!message) {
            alertEl.className = 'hidden mb-6 rounded-2xl px-4 py-3 text-sm';
            alertEl.textContent = '';
            return;
        }

        const classes = {
            info: 'mb-6 rounded-2xl border border-[#E6D3BC] bg-white px-4 py-3 text-sm text-[#6B3D2E]',
            success: 'mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700',
            error: 'mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700',
        };

        alertEl.className = classes[type] || classes.info;
        alertEl.textContent = message;
    }

    async function request(url, method = 'GET', payload = null) {
        const options = {
            method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        };

        if (payload !== null) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(payload);
        }

        const response = await fetch(url, options);
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validationErrors = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : '';
            throw new Error(validationErrors || data.message || 'Request failed.');
        }

        return data;
    }

    function updateConnectionBadge(label, tone = 'default') {
        const classMap = {
            default: 'rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#6B3D2E]',
            success: 'rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700',
            warning: 'rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-amber-700',
            error: 'rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-red-700',
        };

        roomConnectionStatus.className = classMap[tone] || classMap.default;
        roomConnectionStatus.textContent = label;
    }

    function setSlotPlaceholder(slot, text) {
        slot.innerHTML = '';
        const placeholderText = document.createElement('div');
        placeholderText.className = 'px-6 text-center text-sm text-[#D7BE8A]';
        placeholderText.textContent = text;
        slot.appendChild(placeholderText);
    }

    function clearRemoteAudio() {
        remoteAudioSlot.innerHTML = '';
        remoteAudioSlot.classList.add('hidden');
    }

    function resetMediaStage() {
        setSlotPlaceholder(localVideoSlot, 'Camera preview will appear here.');
        setSlotPlaceholder(remoteVideoSlot, 'Your speaking partner will appear here.');
        localMediaStatus.textContent = 'Waiting for camera';
        remoteMediaStatus.textContent = 'Waiting to join';
        clearRemoteAudio();
    }

    function attachVideoTrack(track, slot) {
        slot.innerHTML = '';
        const element = track.attach();
        element.className = 'h-full w-full rounded-[1.25rem] object-cover';
        slot.appendChild(element);
    }

    function detachTrackElements(track) {
        if (!track || typeof track.detach !== 'function') {
            return;
        }

        track.detach().forEach((element) => element.remove());
    }

    function attachRemoteAudio(track) {
        clearRemoteAudio();
        const audioEl = track.attach();
        audioEl.autoplay = true;
        remoteAudioSlot.appendChild(audioEl);
        remoteAudioSlot.classList.remove('hidden');
    }

    async function disconnectRoom() {
        if (state.room) {
            try {
                state.room.disconnect();
            } catch (error) {
                // ignore
            }
        }

        state.room = null;
        state.connectedSessionId = null;
        resetMediaStage();
        updateConnectionBadge('Waiting');
    }

    function renderQueueStatus() {
        if (state.session) {
            queueCard.textContent = state.session.status === 'active'
                ? 'You are already in a live call, so random matching is paused.'
                : 'You already have a pending call session, so random matching is paused.';
            randomButton.textContent = state.session.status === 'active' ? 'Already in a call' : 'Call session pending';
            randomButton.disabled = true;
            return;
        }

        randomButton.disabled = !configured;

        if (state.searching) {
            queueCard.textContent = 'Matching in progress. Keep this page open while we look for another learner.';
            randomButton.textContent = 'Cancel matching';
            return;
        }

        queueCard.textContent = 'You are not in the queue right now.';
        randomButton.textContent = 'Find a random partner';
    }

    function renderSession(session) {
        state.session = session;
        sessionBadges.innerHTML = '';

        if (!session) {
            incomingCard.classList.add('hidden');
            sessionSummaryCard.classList.add('hidden');
            leaveCallButton.classList.add('hidden');
            roomShell.classList.add('hidden');
            placeholder.classList.remove('hidden');
            roomMeta.textContent = 'Preparing your room connection...';
            renderQueueStatus();
            return;
        }

        placeholder.classList.add('hidden');
        sessionSummaryCard.classList.remove('hidden');
        sessionAvatar.textContent = session.other_participant.avatar || 'U';
        sessionPartnerName.textContent = session.other_participant.name || 'Unknown partner';
        sessionMeta.textContent = `${session.mode === 'random' ? 'Random match' : 'Friend call'} • ${session.status === 'active' ? 'Live now' : session.is_incoming ? 'Waiting for your answer' : 'Waiting for your partner'}`;

        const statusBadge = document.createElement('span');
        statusBadge.className = 'rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#6B3D2E]';
        statusBadge.textContent = session.status;
        sessionBadges.appendChild(statusBadge);

        const providerBadge = document.createElement('span');
        providerBadge.className = 'rounded-full bg-[#4A2C2A]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#4A2C2A]';
        providerBadge.textContent = 'LiveKit';
        sessionBadges.appendChild(providerBadge);

        remoteParticipantName.textContent = session.other_participant.name || 'Partner';

        if (session.status === 'ringing' && session.is_incoming) {
            incomingCard.classList.remove('hidden');
            incomingTitle.textContent = `${session.other_participant.name || 'A friend'} is calling you`;
            leaveCallButton.classList.add('hidden');
            roomShell.classList.add('hidden');
            updateConnectionBadge('Incoming', 'warning');
        } else {
            incomingCard.classList.add('hidden');

            if (session.status === 'active' && session.connection) {
                leaveCallButton.classList.remove('hidden');
                roomShell.classList.remove('hidden');
                roomMeta.textContent = `${session.connection.room_name} • token expires ${new Date(session.connection.expires_at).toLocaleTimeString()}`;
            } else {
                leaveCallButton.classList.remove('hidden');
                roomShell.classList.add('hidden');
                updateConnectionBadge('Waiting');
            }
        }

        renderQueueStatus();
    }

    function renderFriends(friends) {
        onlineFriendCount.textContent = String(friends.length);

        if (!friends.length) {
            friendList.innerHTML = `
                <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-[#FBF7F1] px-5 py-8 text-center text-sm text-[#8B6B47]">
                    No friends are available in the video room right now.
                </div>
            `;
            return;
        }

        friendList.innerHTML = friends.map((friend) => `
            <article class="rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] p-4">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#D4B970] text-sm font-black uppercase tracking-[0.08em] text-[#4A2C2A]">
                        ${friend.avatar || 'U'}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-black text-[#4A2C2A]">${friend.name || 'Unknown friend'}</div>
                        <div class="mt-1 truncate text-xs text-[#8B6B47]">${friend.email || ''}</div>
                        <div class="mt-1 text-xs text-[#8B6B47]">${friend.last_seen_label || 'Recently active'}</div>
                    </div>
                </div>
                <div class="mt-4">
                    <button data-friend-call="${friend.id}"
                            class="w-full rounded-2xl px-4 py-3 text-sm font-semibold transition ${friend.is_busy ? 'cursor-not-allowed border border-[#D9C7B5] text-[#8B6B47] bg-white' : 'bg-[#4A2C2A] text-white hover:bg-[#6B3D2E]'}"
                            ${friend.is_busy ? 'disabled' : ''}>
                        ${friend.is_busy ? 'Busy in another call' : 'Call this friend'}
                    </button>
                </div>
            </article>
        `).join('');
    }

    async function connectLiveKit(session) {
        if (!session || session.status !== 'active' || !session.connection) {
            await disconnectRoom();
            return;
        }

        if (state.connectedSessionId === session.id && state.room) {
            return;
        }

        if (!window.LivekitClient?.Room) {
            showAlert('LiveKit client failed to load in this browser.', 'error');
            return;
        }

        await disconnectRoom();
        updateConnectionBadge('Connecting', 'warning');

        const { Room, RoomEvent } = window.LivekitClient;
        const room = new Room({
            adaptiveStream: true,
            dynacast: true,
        });

        const resetRemoteParticipant = () => {
            remoteParticipantName.textContent = session.other_participant.name || 'Partner';
            remoteMediaStatus.textContent = 'Waiting to join';
            setSlotPlaceholder(remoteVideoSlot, 'Your speaking partner will appear here.');
            clearRemoteAudio();
        };

        room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
            if (track.kind === 'video') {
                attachVideoTrack(track, remoteVideoSlot);
                remoteParticipantName.textContent = participant.name || session.other_participant.name || 'Partner';
                remoteMediaStatus.textContent = 'Video connected';
            }

            if (track.kind === 'audio') {
                attachRemoteAudio(track);
                remoteParticipantName.textContent = participant.name || session.other_participant.name || 'Partner';
                remoteMediaStatus.textContent = 'Audio connected';
            }
        });

        room.on(RoomEvent.TrackUnsubscribed, (track) => {
            detachTrackElements(track);
            if (track.kind === 'video') {
                setSlotPlaceholder(remoteVideoSlot, 'Your speaking partner will appear here.');
                remoteMediaStatus.textContent = 'Video paused';
            }
            if (track.kind === 'audio') {
                clearRemoteAudio();
            }
        });

        room.on(RoomEvent.ParticipantDisconnected, () => {
            resetRemoteParticipant();
        });

        room.on(RoomEvent.LocalTrackPublished, (publication) => {
            if (publication.track?.kind === 'video') {
                attachVideoTrack(publication.track, localVideoSlot);
                localMediaStatus.textContent = 'Camera connected';
            }
        });

        room.on(RoomEvent.Disconnected, () => {
            updateConnectionBadge('Disconnected');
        });

        try {
            await room.connect(session.connection.url, session.connection.token);
            await room.localParticipant.enableCameraAndMicrophone();

            room.localParticipant.videoTrackPublications.forEach((publication) => {
                if (publication.track) {
                    attachVideoTrack(publication.track, localVideoSlot);
                    localMediaStatus.textContent = 'Camera connected';
                }
            });

            room.remoteParticipants.forEach((participant) => {
                participant.trackPublications.forEach((publication) => {
                    if (publication.isSubscribed && publication.track) {
                        if (publication.track.kind === 'video') {
                            attachVideoTrack(publication.track, remoteVideoSlot);
                            remoteParticipantName.textContent = participant.name || session.other_participant.name || 'Partner';
                            remoteMediaStatus.textContent = 'Video connected';
                        }

                        if (publication.track.kind === 'audio') {
                            attachRemoteAudio(publication.track);
                            remoteParticipantName.textContent = participant.name || session.other_participant.name || 'Partner';
                            remoteMediaStatus.textContent = 'Audio connected';
                        }
                    }
                });
            });

            state.room = room;
            state.connectedSessionId = session.id;
            updateConnectionBadge('Connected', 'success');
        } catch (error) {
            await disconnectRoom();
            updateConnectionBadge('Failed', 'error');
            showAlert(error?.message || 'LiveKit connection failed.', 'error');
        }
    }

    async function refreshStatus(showErrors = false) {
        try {
            const data = await request(endpoints.status);
            state.searching = Boolean(data.is_searching);
            renderSession(data.current_session);
            renderFriends(data.online_friends || []);

            if (data.current_session?.status === 'active' && data.current_session?.connection) {
                await connectLiveKit(data.current_session);
            } else if (!data.current_session || data.current_session.status !== 'active') {
                await disconnectRoom();
            }
        } catch (error) {
            if (showErrors) {
                showAlert(error.message, 'error');
            }
        }
    }

    async function sendHeartbeat() {
        try {
            await request(endpoints.heartbeat, 'POST', {
                path: window.location.pathname,
                video_ready: true,
            });
        } catch (error) {
            return;
        }
    }

    async function handleRandomMatch() {
        try {
            randomButton.disabled = true;
            const data = state.searching
                ? await request(endpoints.cancelRandom, 'DELETE')
                : await request(endpoints.random, 'POST');

            showAlert(data.message || (state.searching ? 'Matching cancelled.' : 'Matching started.'), 'success');
            await refreshStatus();
        } catch (error) {
            showAlert(error.message, 'error');
        } finally {
            renderQueueStatus();
        }
    }

    async function handleFriendCall(friendId) {
        try {
            const data = await request(`${endpoints.inviteFriendBase}/${friendId}`, 'POST');
            showAlert(data.message || 'Friend call invitation sent.', 'success');
            await refreshStatus();
        } catch (error) {
            showAlert(error.message, 'error');
        }
    }

    async function handleSessionAction(action) {
        if (!state.session) {
            return;
        }

        try {
            const url = `${endpoints.acceptBase}/${state.session.id}/${action}`;
            const data = await request(url, 'POST');

            if (action === 'leave' || action === 'decline') {
                await disconnectRoom();
            }

            showAlert(data.message || 'Action completed.', 'success');
            await refreshStatus();
        } catch (error) {
            showAlert(error.message, 'error');
        }
    }

    randomButton?.addEventListener('click', handleRandomMatch);
    acceptCallButton?.addEventListener('click', () => handleSessionAction('accept'));
    declineCallButton?.addEventListener('click', () => handleSessionAction('decline'));
    leaveCallButton?.addEventListener('click', () => handleSessionAction('leave'));

    friendList?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-friend-call]');
        if (!button) {
            return;
        }

        handleFriendCall(button.getAttribute('data-friend-call'));
    });

    resetMediaStage();
    renderQueueStatus();
    refreshStatus(true);
    sendHeartbeat();

    state.pollTimer = window.setInterval(() => refreshStatus(false), 5000);
    state.heartbeatTimer = window.setInterval(sendHeartbeat, 25000);
})();
</script>
@endpush
