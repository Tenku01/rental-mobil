<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectByRole($request);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectByRole($request);
    }

    /**
     * 🔹 Tentukan redirect berdasarkan role user
     */
   private function redirectByRole(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Mapping Role ID ke Route Name Dashboard
        // Role ID: 1=Admin, 2=Pelanggan, 3=Resepsionis, 4=Sopir, 5=Staff
        $routeDashboard = match ($user->role_id) {
            1 => 'admin.dashboard',       // Route: admin.dashboard
            3 => 'resepsionis.dashboard', // Route: resepsionis.dashboard
            4 => 'sopir.dashboard',       // Route: sopir.dashboard
            5 => 'staff.dashboard',       // Route: staff.dashboard
            default => 'dashboard',       // Route: dashboard (Pelanggan)
        };

        return redirect()->intended(route($routeDashboard, absolute: false) . '?verified=1');
    }
}
