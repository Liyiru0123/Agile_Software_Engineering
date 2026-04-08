@extends('layouts.app')

@section('title', 'AI Conversation')

@push('styles')
<style>
    #companion-shell {
        display: none !important;
    }
    .live2d-subtitle-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[radial-gradient(circle_at_top,_#f8f2e8,_#f0ddc4_36%,_#c18967_68%,_#5c3732_100%)] py-8">
    <div class="mx-auto flex min-h-[calc(100vh-8rem)] max-w-7xl flex-col px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('speaking.hub') }}" class="inline-flex items-center rounded-full border border-white/20 bg-white/12 px-4 py-2 text-sm font-semibold text-white/88 transition hover:bg-white/18">
                Back to Speaking Hub
            </a>
            <div class="rounded-full border border-white/15 bg-[#4A2C2A]/68 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#F5E6D3]">
                {{ $live2dInterface['status'] === 'available' ? 'Live Mode' : 'Setup Mode' }}
            </div>
        </div>

        <div class="mt-6 grid flex-1 gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="relative overflow-hidden rounded-[2.5rem] border border-white/18 bg-[linear-gradient(180deg,rgba(255,255,255,0.18)_0%,rgba(255,255,255,0.08)_48%,rgba(74,44,42,0.12)_100%)] shadow-[0_38px_90px_rgba(44,24,16,0.22)] backdrop-blur-md">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.28),rgba(255,255,255,0.06)_42%,rgba(74,44,42,0.12)_100%)]"></div>
                <div class="relative flex h-full min-h-[760px] flex-col">
                    <div class="px-6 pt-6 sm:px-8">
                        <div class="inline-flex items-center rounded-full bg-white/14 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white/82">
                            Live2D AI Conversation
                        </div>
                        <h1 class="mt-4 text-3xl font-black tracking-tight text-white sm:text-4xl">
                            Hiyori stays centered. Her reply appears as subtitles.
                        </h1>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-white/82 sm:text-base">
                            Use text or browser voice input to talk with Hiyori. To keep cost low, the browser handles speech capture and speech playback first, while the server only generates the text reply.
                        </p>
                    </div>

                    <div class="relative flex flex-1 items-center justify-center px-4 pb-24 pt-6 sm:px-8">
                        <div class="absolute left-1/2 top-1/2 h-[440px] w-[440px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[radial-gradient(circle,_rgba(255,248,229,0.52),rgba(255,248,229,0.12)_48%,rgba(255,248,229,0)_72%)] blur-2xl"></div>
                        <div class="absolute bottom-20 left-1/2 h-28 w-[68%] -translate-x-1/2 rounded-[50%] bg-[radial-gradient(circle_at_center,_rgba(255,239,209,0.92),rgba(255,239,209,0.34)_48%,rgba(255,239,209,0)_80%)]"></div>

                        <div id="live2d-stage" class="relative z-10 h-[520px] w-full max-w-[760px] cursor-pointer select-none"></div>
                        <div id="live2d-stage-fallback" class="pointer-events-none absolute inset-x-0 top-1/2 z-20 -translate-y-1/2 text-center text-sm font-medium text-white/86">
                            Loading Hiyori scene...
                        </div>

                        <div class="pointer-events-none absolute inset-x-0 bottom-5 z-20 px-4 sm:px-8">
                            <div id="live2d-subtitle-shell" class="mx-auto max-w-4xl rounded-[2rem] border border-white/24 bg-black/52 px-5 py-4 shadow-[0_20px_45px_rgba(22,12,10,0.28)] opacity-100">
                                <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#D4B970]">Subtitle</div>
                                <div id="live2d-subtitle" class="mt-2 text-center text-base font-medium leading-8 text-white sm:text-lg">
                                    Hiyori is ready. Say hello or ask for practice help.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-20 border-t border-white/12 bg-[#4A2C2A]/50 px-4 py-5 backdrop-blur-md sm:px-8">
                        <div class="mx-auto max-w-4xl">
                            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto_auto_auto]">
                                <input id="live2d-chat-input"
                                       type="text"
                                       maxlength="500"
                                       placeholder="Type to Hiyori, or use the microphone."
                                       class="w-full rounded-[1.4rem] border border-white/16 bg-white/92 px-5 py-4 text-sm text-[#4A2C2A] shadow-sm outline-none transition focus:border-[#D4B970] focus:ring-2 focus:ring-[#F2E2C7]">
                                <button id="live2d-send-button" type="button" class="rounded-[1.4rem] bg-[#F5E6D3] px-5 py-4 text-sm font-bold uppercase tracking-[0.14em] text-[#4A2C2A] transition hover:bg-white">
                                    Send
                                </button>
                                <button id="live2d-voice-button" type="button" class="rounded-[1.4rem] border border-white/20 bg-white/12 px-5 py-4 text-sm font-bold uppercase tracking-[0.14em] text-white transition hover:bg-white/16">
                                    Start Voice
                                </button>
                                <button id="live2d-reset-button" type="button" class="rounded-[1.4rem] border border-white/20 bg-white/12 px-5 py-4 text-sm font-bold uppercase tracking-[0.14em] text-white transition hover:bg-white/16">
                                    Reset
                                </button>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div class="rounded-[1.5rem] border border-white/14 bg-white/10 px-4 py-4">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.16em] text-white/72">Status</div>
                                    <div id="live2d-status" class="mt-2 text-sm leading-6 text-white/88">
                                        Idle. Send a message or start browser voice input.
                                    </div>
                                </div>
                                <div class="rounded-[1.5rem] border border-white/14 bg-white/10 px-4 py-4">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.16em] text-white/72">You Said</div>
                                    <div id="live2d-user-transcript" class="mt-2 text-sm leading-6 text-white/88">
                                        Nothing yet.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <section class="rounded-[2rem] border border-white/18 bg-white/14 p-5 text-white shadow-[0_24px_55px_rgba(44,24,16,0.18)] backdrop-blur-md">
                    <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-white/68">Mode</div>
                    <div class="mt-3 text-2xl font-black">Lowest-Cost Setup</div>
                    <div class="mt-3 space-y-2 text-sm leading-7 text-white/82">
                        <p>Speech input: Browser microphone recognition.</p>
                        <p>AI reply: Server-side text generation.</p>
                        <p>Speech output: Browser speech synthesis.</p>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/18 bg-[#4A2C2A]/74 p-5 text-[#F5E6D3] shadow-[0_24px_55px_rgba(44,24,16,0.2)] backdrop-blur-md">
                    <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-[#D4B970]">Notes</div>
                    <div class="mt-4 space-y-3 text-sm leading-7">
                        @foreach($live2dInterface['notes'] as $note)
                            <p>{{ $note }}</p>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/18 bg-white/14 p-5 text-white shadow-[0_24px_55px_rgba(44,24,16,0.18)] backdrop-blur-md">
                    <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-white/68">Endpoint</div>
                    <div class="mt-3 break-all rounded-[1.25rem] bg-[#2C1810]/26 px-4 py-4 text-sm leading-6 text-white/84">
                        {{ $live2dInterface['conversation_endpoint'] }}
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const stage = document.getElementById('live2d-stage');
    const fallback = document.getElementById('live2d-stage-fallback');
    const subtitle = document.getElementById('live2d-subtitle');
    const status = document.getElementById('live2d-status');
    const transcript = document.getElementById('live2d-user-transcript');
    const input = document.getElementById('live2d-chat-input');
    const sendButton = document.getElementById('live2d-send-button');
    const voiceButton = document.getElementById('live2d-voice-button');
    const resetButton = document.getElementById('live2d-reset-button');
    const endpoint = @json($live2dInterface['conversation_endpoint']);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const modelUrl = @json(asset('live2d/hiyori/hiyori_free_t08.model3.json'));

    let app = null;
    let model = null;
    let recognition = null;
    let isListening = false;
    let isSending = false;
    let preferredVoice = null;

    function setStatus(message) {
        status.textContent = message;
    }

    function setSubtitle(message) {
        subtitle.textContent = message;
    }

    function setTranscript(message) {
        transcript.textContent = message;
    }

    function pickPreferredVoice() {
        if (!('speechSynthesis' in window)) {
            return null;
        }

        const voices = window.speechSynthesis.getVoices();
        if (!voices.length) {
            return null;
        }

        const preferredPatterns = [
            /aria/i,
            /ava/i,
            /samantha/i,
            /allison/i,
            /zoe/i,
            /emma/i,
            /jenny/i,
            /female/i,
            /woman/i,
            /girl/i,
        ];

        const englishVoices = voices.filter((voice) => /^en(-|_)/i.test(voice.lang || ''));
        const femaleEnglish = englishVoices.find((voice) =>
            preferredPatterns.some((pattern) => pattern.test(voice.name || ''))
        );

        preferredVoice = femaleEnglish
            || englishVoices.find((voice) => /en-us/i.test(voice.lang || ''))
            || englishVoices[0]
            || voices[0]
            || null;

        return preferredVoice;
    }

    function speakOutLoud(text) {
        if (!('speechSynthesis' in window) || !text) {
            return;
        }

        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = 1.0;
        utterance.pitch = 1.22;

        const voice = preferredVoice || pickPreferredVoice();
        if (voice) {
            utterance.voice = voice;
            utterance.lang = voice.lang || utterance.lang;
        }

        window.speechSynthesis.speak(utterance);
    }

    function setSendingState(sending) {
        isSending = sending;
        sendButton.disabled = sending;
        input.disabled = sending;
        resetButton.disabled = sending;
        if (sending) {
            sendButton.textContent = 'Sending...';
        } else {
            sendButton.textContent = 'Send';
        }
    }

    async function postJson(payload) {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'The conversation request failed.');
        }

        return data;
    }

    async function sendMessage(text) {
        const trimmed = (text || '').trim();
        if (!trimmed || isSending) {
            return;
        }

        setTranscript(trimmed);
        setStatus('Hiyori is thinking...');
        setSubtitle('Thinking...');
        setSendingState(true);

        try {
            const data = await postJson({ message: trimmed });
            setSubtitle(data.assistant_text || 'No reply returned.');
            setStatus(data.notice || 'Reply ready.');
            speakOutLoud(data.assistant_text || '');
            input.value = '';
        } catch (error) {
            setSubtitle('I could not answer that request just now.');
            setStatus(error.message || 'The conversation request failed.');
        } finally {
            setSendingState(false);
        }
    }

    async function resetConversation() {
        if (isSending) {
            return;
        }

        setStatus('Clearing the conversation...');

        try {
            const data = await postJson({ reset: true });
            setTranscript('Nothing yet.');
            setSubtitle(data.assistant_text || 'Conversation cleared.');
            setStatus('Conversation cleared.');
            input.value = '';
        } catch (error) {
            setStatus(error.message || 'Unable to clear the conversation right now.');
        }
    }

    function setupVoiceRecognition() {
        const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!Recognition) {
            voiceButton.disabled = true;
            setStatus('Browser voice input is unavailable here. Type to Hiyori instead.');
            return;
        }

        recognition = new Recognition();
        recognition.lang = 'en-US';
        recognition.interimResults = true;
        recognition.continuous = false;
        recognition.maxAlternatives = 1;

        recognition.onstart = () => {
            isListening = true;
            voiceButton.textContent = 'Stop Voice';
            setStatus('Listening through the browser microphone...');
        };

        recognition.onresult = (event) => {
            let finalTranscript = '';
            let interimTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; i += 1) {
                const chunk = event.results[i][0]?.transcript || '';
                if (event.results[i].isFinal) {
                    finalTranscript += chunk + ' ';
                } else {
                    interimTranscript += chunk + ' ';
                }
            }

            setTranscript((finalTranscript || interimTranscript || 'Listening...').trim());

            if (finalTranscript.trim()) {
                input.value = finalTranscript.trim();
            }
        };

        recognition.onerror = (event) => {
            isListening = false;
            voiceButton.textContent = 'Start Voice';
            setStatus(`Voice input error: ${event.error}.`);
        };

        recognition.onend = () => {
            const finalText = input.value.trim();
            isListening = false;
            voiceButton.textContent = 'Start Voice';

            if (!finalText) {
                setStatus('Listening stopped. No final speech was captured.');
                return;
            }

            sendMessage(finalText);
        };

        voiceButton.addEventListener('click', () => {
            if (!recognition || isSending) {
                return;
            }

            if (isListening) {
                recognition.stop();
                return;
            }

            input.value = '';
            recognition.start();
        });
    }

    async function loadScript(sources, id) {
        if (document.getElementById(id)) {
            return;
        }

        const candidates = Array.isArray(sources) ? sources : [sources];
        let lastError = null;

        for (const src of candidates) {
            try {
                await new Promise((resolve, reject) => {
                    const existing = document.getElementById(id);
                    if (existing) {
                        existing.remove();
                    }

                    const script = document.createElement('script');
                    script.id = id;
                    script.src = src;
                    script.async = true;
                    script.onload = resolve;
                    script.onerror = () => reject(new Error('Failed to load script: ' + src));
                    document.head.appendChild(script);
                });

                return;
            } catch (error) {
                lastError = error;
            }
        }

        throw lastError || new Error('Failed to load runtime script.');
    }

    function fitModel() {
        if (!app || !model || !stage) {
            return;
        }

        const width = stage.clientWidth;
        const height = stage.clientHeight;
        app.renderer.resize(width, height);

        const scale = Math.min(width / model.width, height / model.height) * 1.6;
        model.scale.set(scale);
        model.x = (width - model.width) / 2;
        model.y = Math.max(-10, height - model.height + 22);
    }

    function playMotion() {
        if (!model || typeof model.motion !== 'function') {
            return;
        }

        for (const group of ['Tap', 'Tap@Body', 'Flick', 'Flick@Body', 'Idle']) {
            try {
                model.motion(group);
                return;
            } catch (error) {
                continue;
            }
        }
    }

    async function loadScene() {
        try {
            await loadScript([
                'https://cdn.jsdelivr.net/npm/pixi.js@6.5.10/dist/browser/pixi.min.js',
                'https://unpkg.com/pixi.js@6.5.10/dist/browser/pixi.min.js'
            ], 'live2d-pixi');
            await loadScript([
                'https://cdn.jsdelivr.net/npm/live2dcubismcore@1.0.2/live2dcubismcore.min.js',
                'https://unpkg.com/live2dcubismcore@1.0.2/live2dcubismcore.min.js'
            ], 'live2d-cubism-core');
            await loadScript([
                'https://cdn.jsdelivr.net/npm/pixi-live2d-display@0.4.0/dist/cubism4.min.js',
                'https://unpkg.com/pixi-live2d-display@0.4.0/dist/cubism4.min.js'
            ], 'live2d-runtime');

            if (!window.PIXI?.live2d?.Live2DModel) {
                throw new Error('Live2D runtime missing.');
            }

            fallback?.remove();
            app = new window.PIXI.Application({
                autoStart: true,
                backgroundAlpha: 0,
                resizeTo: stage,
                antialias: true,
                autoDensity: true,
            });
            stage.appendChild(app.view);

            model = await window.PIXI.live2d.Live2DModel.from(modelUrl);
            model.interactive = true;
            model.buttonMode = true;
            app.stage.addChild(model);
            fitModel();
            window.addEventListener('resize', fitModel);

            model.on('pointertap', () => {
                playMotion();
            });

            playMotion();
        } catch (error) {
            if (fallback) {
                fallback.textContent = `Hiyori scene failed. ${error?.message || 'Runtime unavailable.'}`;
            }
            setStatus(`Live2D scene failed to load. ${error?.message || 'Runtime unavailable.'}`);
        }
    }

    sendButton.addEventListener('click', () => sendMessage(input.value));
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendMessage(input.value);
        }
    });
    resetButton.addEventListener('click', resetConversation);
    stage.addEventListener('click', playMotion);

    pickPreferredVoice();
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = () => {
            pickPreferredVoice();
        };
    }

    setupVoiceRecognition();
    loadScene();
})();
</script>
@endpush
