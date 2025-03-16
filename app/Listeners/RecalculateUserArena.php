<?php

namespace App\Listeners;

use App\Actions\HandleArenaRanking;
use App\Events\ArenaRankingChanged;
use App\Models\Arena;
use App\Models\Revenue;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;

class RecalculateUserArena
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
    public function handle(Revenue $revenue): void
    {
        $action = app(HandleArenaRanking::class);

        $user = User::query()->find($revenue->user_id);

        $oldArena = Arena::query()->find($user->arena_id);
        $newArena = $this->matchingArena($revenue->refresh()->turnover);

        if ($revenue->occurs_at->isSameMonth(Carbon::now())) {
            $action->processArenaChange($user, $oldArena, $newArena, $revenue->occurs_at);
        } else {
            $action->trackPreviousArenaRanking($user, $newArena, $revenue->occurs_at);
        }
    }

    private function matchingArena(float $turnover)
    {
        return Arena::query()
            ->where('minimum_threshold', '<=', $turnover)
            ->latest('minimum_threshold')
            ->first();
    }
}
