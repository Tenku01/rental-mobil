<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SopirMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Pastikan user memiliki role sopir (role_id = 4)
        if ($user->role_id !== 4) {
            abort(403, 'Akses ditolak. Hanya untuk sopir.');
        }

        return $next($request);
    }
}
