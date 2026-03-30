<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Academic English')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-[#FAF0E6]">
    <nav class="bg-[#4A2C2A] border-b-4 border-[#2C1810] shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <svg class="w-8 h-8 text-[#C9A961] group-hover:text-[#D4B970] transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-serif font-bold text-[#F5E6D3]">Academic English</h1>
                        <p class="text-xs text-[#C9A961]">Your Learning Journey</p>
                    </div>
                </a>

                <div class="flex items-center gap-2 ml-8">
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('home') ? 'bg-[#6B3D2E]' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('articles.index') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('articles.*') ? 'bg-[#6B3D2E]' : '' }}">
                        Library
                    </a>
                </div>
            </div>

            @auth
                <div class="flex items-center gap-4">
                    <span class="text-[#F5E6D3] text-sm font-medium">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg hover:bg-[#8B4D3A] transition font-medium text-sm shadow-md">
                            Logout
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    @yield('content')

    @stack('scripts')
</body>
</html>
