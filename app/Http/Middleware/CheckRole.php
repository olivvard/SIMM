<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            return back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        return $next($request);
    }
}
