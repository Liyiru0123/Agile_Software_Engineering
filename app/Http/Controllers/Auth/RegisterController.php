<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * 处理注册请求
     */
    public function register(Request $request)
    {
        // 1. 验证输入
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Please enter your full name',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Please enter a password',
            'password.confirmed' => 'Passwords do not match',
            'password.min' => 'Password must be at least 8 characters',
            'terms.accepted' => 'You must accept the terms and conditions',
        ]);
        
        // 2. 创建用户
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // 自动验证邮箱（生产环境应发送邮件）
        ]);
        
        // 3. 自动登录
        Auth::login($user);
        
        // 4. 重定向
        return redirect('/')->with('success', 'Account created successfully!');
    }
}