<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        // Validate credentials and log in
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Admin redirects to backend, regular user redirects to homepage
            return Auth::user()->is_admin 
                ? redirect()->intended('/admin')
                : redirect()->intended('/');
        }

        // Login failed
        throw ValidationException::withMessages([
            'email' => ['Incorrect email or password'],
        ]);
    }

    // Show registration form
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Handle registration request
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create regular user (is_admin defaults to false)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        // Auto login after registration
        Auth::login($user);

        return redirect('/');
    }

    // Handle logout request (Final fixed version: Compatible with AJAX + page redirect)
    public function logout(Request $request)
    {
        // 1. Force destroy web guard authentication state
        Auth::guard('web')->logout();
        
        // 2. Completely clear session data to avoid residue
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // 3. Compatible with AJAX and normal form requests
        if ($request->wantsJson()) {
            return response()->json([
                'code' => 0,
                'message' => 'Logout successful',
                'redirect' => route('home')
            ]);
        }
        
        // 4. Normal request redirect (Admin to login page, regular user to homepage)
        $redirectUrl = $request->user()?->is_admin ? route('login') : route('home');
        return redirect($redirectUrl)->with('success', 'Logout successful!');
    }
}