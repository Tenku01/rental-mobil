<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResepsionisMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Pastikan user memiliki role resepsionis (misal role_id = 2)
        if ($user->role_id !== 3) {
            abort(403, 'Akses ditolak. Hanya untuk resepsionis.');
        }

        return $next($request);
    }
}
