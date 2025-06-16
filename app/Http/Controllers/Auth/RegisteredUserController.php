<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
// use App\Providers\RouteServiceProvider; // Hapus atau biarkan jika masih dipakai
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules; // Pastikan ini di-import

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): \Illuminate\View\View // Perbarui tipe return jika menggunakan PHP 8+
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            // Modifikasi aturan password di sini:
            'password' => [
                'required',
                'confirmed', // Memastikan field 'password_confirmation' cocok
                Rules\Password::min(8) // Minimal 8 karakter
                    // Anda juga bisa menambahkan aturan lain jika perlu:
                    ->mixedCase()      // Harus ada huruf besar dan kecil
                    ->letters()        // Harus ada huruf
                    ->numbers()        // Harus ada angka
                    ->symbols()        // Harus ada simbol
                    // ->uncompromised()  // Cek apakah password pernah bocor (butuh API key)
            ],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Ganti ini jika RouteServiceProvider::HOME error
        // return redirect(RouteServiceProvider::HOME);
        return redirect(route('dashboard')); // Atau return redirect('/dashboard');
    }
}