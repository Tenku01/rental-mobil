<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sedang login
        // 2. Cek apakah role_id user adalah 1 (Admin)
        if (Auth::check() && Auth::user()->role_id === 1) {
            return $next($request);
        }

        // Jika kondisi di atas tidak terpenuhi, tolak akses dengan error 403 Forbidden
        abort(403, 'AKSES DITOLAK: Halaman ini khusus Administrator.');
    }
}