<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Tangani permintaan login masuk.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Membuat key throttle berdasarkan email dan IP
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        // Cek apakah user telah melebihi batas percobaan login
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => __("Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik."),
            ]);
        }

        // Coba autentikasi
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Gagal login → tambahkan 1 hit ke rate limiter
            RateLimiter::hit($throttleKey, 60); // 60 detik penundaan per hit

            throw ValidationException::withMessages([
                'email' => __('Email atau password salah.'),
            ]);
        }

        // Jika berhasil login, reset percobaan
        RateLimiter::clear($throttleKey);

        // Login berhasil → buat ulang session
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Logout user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}