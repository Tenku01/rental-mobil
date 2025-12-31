<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        //
    ];

    public function boot(): void
    {
        // Daftarkan observer untuk model User
        User::observe(UserObserver::class);
    }
}
