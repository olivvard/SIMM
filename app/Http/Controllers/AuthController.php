<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function getCaptcha()
    {
        // Dynamic 5-character alphanumeric captcha
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $captchaCode = '';
        for ($i = 0; $i < 5; $i++) {
            $captchaCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        session(['captcha_code' => strtolower($captchaCode)]);

        $width = 160;
        $height = 45;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
        $svg .= '<rect width="100%" height="100%" fill="#1e293b"/>';

        // Noise lines
        for ($i = 0; $i < 5; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = rand(0, $width);
            $y2 = rand(0, $height);
            $strokeColors = ['#475569', '#334155', '#64748b', '#3b82f6'];
            $color = $strokeColors[rand(0, count($strokeColors) - 1)];
            $svg .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="' . $color . '" stroke-width="1.5" opacity="0.6"/>';
        }

        // Noise dots
        for ($i = 0; $i < 25; $i++) {
            $cx = rand(0, $width);
            $cy = rand(0, $height);
            $r = rand(1, 2);
            $svg .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="#64748b" opacity="0.4"/>';
        }

        // Render characters with distortion & color
        $colors = ['#38bdf8', '#34d399', '#f43f5e', '#fbbf24', '#a855f7', '#818cf8'];
        $xStep = ($width - 25) / 5;

        for ($i = 0; $i < 5; $i++) {
            $char = $captchaCode[$i];
            $x = 15 + ($i * $xStep) + rand(-2, 2);
            $y = 30 + rand(-2, 2);
            $angle = rand(-12, 12);
            $color = $colors[rand(0, count($colors) - 1)];

            $svg .= '<text x="' . $x . '" y="' . $y . '" fill="' . $color . '" font-size="24" font-weight="bold" font-family="monospace" transform="rotate(' . $angle . ' ' . $x . ' ' . $y . ')">' . $char . '</text>';
        }

        $svg .= '</svg>';

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'captcha'  => ['required', 'string'],
        ]);

        // Validate Captcha
        if (strtolower($credentials['captcha']) !== session('captcha_code')) {
            session()->forget('captcha_code');

            return back()->withErrors([
                'captcha' => 'Kode Captcha yang Anda masukkan salah. Silakan coba lagi.',
            ])->onlyInput('username');
        }

        $throttleKey = Str::lower($credentials['username']) . '|' . $request->ip();

        // Check Rate Limiter (Max 5 attempts, 5 minute penalty = 300 seconds)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            return back()->withErrors([
                'username' => 'Terlalu banyak percobaan login gagal. Akun Anda dikunci sementara. Silakan coba lagi dalam ' . $minutes . ' menit.',
            ])->onlyInput('username');
        }

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            session()->forget('captcha_code');
            $request->session()->regenerate();

            $user = Auth::user();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->full_name . '!');
        }

        // Increment rate limiter decay to 5 minutes (300s)
        RateLimiter::hit($throttleKey, 300);
        $remainingAttempts = RateLimiter::remaining($throttleKey, 5);

        $errorMessage = $remainingAttempts > 0
            ? 'Username atau password salah. Sisa percobaan: ' . $remainingAttempts . ' kali.'
            : 'Terlalu banyak percobaan login gagal. Akun Anda dikunci sementara selama 5 menit.';

        return back()->withErrors([
            'username' => $errorMessage,
        ])->onlyInput('username');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'role'      => ['required', 'string', 'in:admin,teknisi'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'password'  => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Registrasi berhasil. Selamat datang di WEA PM Motor!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
