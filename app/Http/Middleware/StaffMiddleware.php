<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Pastikan user memiliki role staff (role_id = 5)
        if ($user->role_id !== 5) {
            abort(403, 'Akses ditolak. Hanya untuk staff.');
        }

        return $next($request);
    }
}
