@extends('layouts.app')

@section('title', 'Register - EAPlus')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-darkWood py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-silkGold/10 p-8 rounded-xl shadow-2xl border border-silkGold/20">
        
        <!-- Logo / Title -->
        <div class="text-center">
            <h2 class="mt-2 text-3xl font-serif font-bold text-silkGold">
                {{ config('app.name', 'EAPlus') }}
            </h2>
            <p class="mt-2 text-sm text-silkGold/70">
                Create your account and start learning
            </p>
        </div>
        
        <!-- Registration Form -->
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="space-y-4">
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-silkGold">
                        Full Name
                    </label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        autocomplete="name" 
                        required 
                        autofocus
                        class="mt-1 block w-full px-4 py-3 bg-darkWood/50 border border-silkGold/30 
                               rounded-lg text-silkGold placeholder-silkGold/40 
                               focus:outline-none focus:ring-2 focus:ring-silkGold/50 focus:border-transparent
                               transition duration-200"
                        placeholder="John Doe"
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-silkGold">
                        Email Address
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="mt-1 block w-full px-4 py-3 bg-darkWood/50 border border-silkGold/30 
                               rounded-lg text-silkGold placeholder-silkGold/40 
                               focus:outline-none focus:ring-2 focus:ring-silkGold/50 focus:border-transparent
                               transition duration-200"
                        placeholder="your@email.com"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-silkGold">
                        Password
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="new-password" 
                        required 
                        class="mt-1 block w-full px-4 py-3 bg-darkWood/50 border border-silkGold/30 
                               rounded-lg text-silkGold placeholder-silkGold/40 
                               focus:outline-none focus:ring-2 focus:ring-silkGold/50 focus:border-transparent
                               transition duration-200"
                        placeholder="••••••••"
                    >
                    <p class="mt-1 text-xs text-silkGold/50">
                        Must be at least 8 characters
                    </p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-silkGold">
                        Confirm Password
                    </label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        autocomplete="new-password" 
                        required 
                        class="mt-1 block w-full px-4 py-3 bg-darkWood/50 border border-silkGold/30 
                               rounded-lg text-silkGold placeholder-silkGold/40 
                               focus:outline-none focus:ring-2 focus:ring-silkGold/50 focus:border-transparent
                               transition duration-200"
                        placeholder="••••••••"
                    >
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Terms & Conditions -->
                <div class="flex items-start">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        required
                        class="mt-1 h-4 w-4 text-amber-600 focus:ring-amber-500 
                               border-silkGold/30 rounded bg-darkWood/50"
                    >
                    <label for="terms" class="ml-2 block text-sm text-silkGold/80">
                        I agree to the 
                        <a href="#" class="text-silkGold hover:underline">Terms of Service</a>
                        and 
                        <a href="#" class="text-silkGold hover:underline">Privacy Policy</a>
                    </label>
                </div>
                @error('terms')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
                
            </div>
            
            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent 
                           rounded-lg shadow-sm text-sm font-medium text-darkWood 
                           bg-silkGold hover:bg-silkGold/90 focus:outline-none focus:ring-2 
                           focus:ring-offset-2 focus:ring-silkGold focus:ring-offset-darkWood
                           transition duration-200"
                >
                    Create Account
                </button>
            </div>
            
        </form>
        
        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-silkGold/70">
                Already have an account? 
                <a href="{{ route('login') }}" 
                   class="font-medium text-silkGold hover:text-silkGold/80 transition">
                    Sign in
                </a>
            </p>
        </div>
        
        <!-- Benefits List -->
        <div class="mt-6 pt-6 border-t border-silkGold/20">
            <p class="text-xs text-silkGold/60 text-center mb-3">Why join us?</p>
            <ul class="space-y-2 text-xs text-silkGold/70">
                <li class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-silkGold" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Access to academic articles
                </li>
                <li class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-silkGold" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Interactive exercises
                </li>
                <li class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-silkGold" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    AI-powered feedback
                </li>
                <li class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-silkGold" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Track your progress
                </li>
            </ul>
        </div>
        
    </div>
    
</div>
@endsection

@push('styles')
<style>
    /* 输入框占位符颜色 */
    ::placeholder {
        color: rgba(234, 216, 177, 0.4);
    }
    
    /* 复选框样式微调 */
    input[type="checkbox"]:checked {
        background-color: #D4AF37;
        border-color: #D4AF37;
    }
</style>
@endpush
