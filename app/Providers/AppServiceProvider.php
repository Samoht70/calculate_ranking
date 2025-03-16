<?php

namespace App\Providers;

use App\Listeners\GrantDefaultArena;
use App\Listeners\RecalculateUserArena;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen('eloquent.creating: App\Models\User', GrantDefaultArena::class);

        Event::listen('eloquent.created: App\Models\Revenue', RecalculateUserArena::class);
        Event::listen('eloquent.updated: App\Models\Revenue', RecalculateUserArena::class);
    }
}
