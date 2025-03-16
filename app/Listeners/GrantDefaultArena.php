<?php

namespace App\Listeners;

use App\Models\Arena;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GrantDefaultArena
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(User $user): void
    {
        $user->arena()->associate(Arena::query()->orderBy('minimum_threshold')->first());
    }
}
