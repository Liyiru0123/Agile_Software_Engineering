@extends('layouts.app')

@section('title', 'AI Conversation')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <a href="{{ route('speaking.hub') }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">Back to Speaking Hub</a>

        <div class="grid lg:grid-cols-[minmax(0,1.2fr)_360px] gap-8 items-start">
            <section class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="px-3 py-1 rounded-full bg-[#4A2C2A]/10 text-[#4A2C2A] text-xs font-semibold">AI Conversation</span>
                    <span class="px-3 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E] text-xs font-semibold">Pending integration</span>
                </div>

                <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">Live2D AI Conversation</h1>
                <p class="text-[#6B3D2E] leading-7 mb-6">
                    This page is reserved for the future Live2D AI conversation mode. The routing and backend interface are kept ready so another team member can connect the avatar, dialogue flow, and AI interaction here.
                </p>

                <div class="rounded-3xl border border-[#E8D9C9] bg-[#FBF7F1] p-6 mb-6">
                    <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-3">Reserved Interface</div>
                    <div class="space-y-3 text-sm text-[#3A2A22] leading-7">
                        <div><strong>Status:</strong> {{ $live2dInterface['status'] }}</div>
                        <div><strong>Endpoint:</strong> <code>{{ $live2dInterface['conversation_endpoint'] }}</code></div>
                        <div><strong>Audio Input:</strong> {{ $live2dInterface['audio_input_supported'] ? 'Expected' : 'Not planned' }}</div>
                        <div><strong>Text Input:</strong> {{ $live2dInterface['message_input_supported'] ? 'Expected' : 'Not planned' }}</div>
                    </div>
                </div>

                <div class="rounded-3xl bg-[#4A2C2A] text-white p-6">
                    <div class="text-xs uppercase tracking-[0.15em] text-[#D7BE8A] font-semibold mb-3">Integration Notes</div>
                    <ul class="space-y-3 text-sm text-[#F5E6D3]/90 leading-6">
                        @foreach($live2dInterface['notes'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h2 class="text-xl font-bold text-[#4A2C2A] mb-3">Next Step</h2>
                    <p class="text-sm text-[#6B3D2E] leading-6 mb-5">
                        If you want to practise speaking right now, go to the article speaking area and choose an article first.
                    </p>
                    <a href="{{ route('articles.index', ['skill' => 'speaking']) }}"
                       class="inline-flex items-center justify-center w-full rounded-2xl bg-[#6B3D2E] hover:bg-[#4A2C2A] text-white px-4 py-3 font-semibold transition">
                        Go to Article Speaking
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
