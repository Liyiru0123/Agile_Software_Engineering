@extends('layouts.app')

@section('title', 'Login - EAPlus')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#FAF0E6] py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg border-2 border-[#6B3D2E]">
        
        <!-- Logo / Title -->
        <div class="text-center">
            <svg class="mx-auto w-12 h-12 text-[#6B3D2E]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
            </svg>
            <h2 class="mt-4 text-2xl font-serif font-bold text-[#4A2C2A]">
                {{ config('app.name', 'EAPlus') }}
            </h2>
            <p class="mt-2 text-sm text-[#6B3D2E]">
                Sign in to continue your learning journey
            </p>
        </div>
        
        <!-- Login Form -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="space-y-4">
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#4A2C2A]">
                        Email Address
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="mt-1 block w-full px-4 py-3 bg-[#FAF0E6] border-2 border-[#6B3D2E] 
                               rounded-lg text-[#4A2C2A] placeholder-[#6B3D2E]/50 
                               focus:outline-none focus:border-[#8B4D3A] focus:ring-1 focus:ring-[#8B4D3A]
                               transition duration-200"
                        placeholder="your@email.com"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#4A2C2A]">
                        Password
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required 
                        class="mt-1 block w-full px-4 py-3 bg-[#FAF0E6] border-2 border-[#6B3D2E] 
                               rounded-lg text-[#4A2C2A] placeholder-[#6B3D2E]/50 
                               focus:outline-none focus:border-[#8B4D3A] focus:ring-1 focus:ring-[#8B4D3A]
                               transition duration-200"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-[#6B3D2E] focus:ring-[#8B4D3A] 
                                   border-[#6B3D2E] rounded bg-[#FAF0E6]"
                        >
                        <label for="remember_me" class="ml-2 block text-sm text-[#4A2C2A] font-medium">
                            Remember me
                        </label>
                    </div>
                    
                    <!-- 忘记密码提示 -->
                    <span class="text-sm text-[#4A2C2A] font-medium hover:text-[#6B3D2E] transition cursor-help" 
                          title="Contact administrator to reset your password">
                        🔐 Forgot password?
                    </span>
                </div>
                
            </div>
            
            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-3 px-4 border-2 border-transparent 
                           rounded-lg shadow-sm text-sm font-medium text-[#F5E6D3] 
                           bg-[#6B3D2E] hover:bg-[#8B4D3A] focus:outline-none focus:ring-2 
                           focus:ring-offset-2 focus:ring-[#6B3D2E] focus:ring-offset-[#FAF0E6]
                           transition duration-200"
                >
                    Sign in
                </button>
            </div>
            
        </form>
        
        <!-- Register Link -->
        <div class="text-center">
            <p class="text-sm text-[#6B3D2E]/70">
                Don't have an account? 
                <a href="{{ route('register') }}" 
                   class="font-medium text-[#6B3D2E] hover:text-[#4A2C2A] transition">
                    Sign up for free
                </a>
            </p>
        </div>
        
        <!-- Demo Account Hint -->
        <div class="mt-4 p-3 bg-[#FAF0E6] rounded-lg border border-[#6B3D2E]/20">
            <p class="text-xs text-[#6B3D2E]/60 text-center">
                💡 Demo: test@english.learning / 123456
            </p>
        </div>
        
    </div>
    
</div>
@endsection
