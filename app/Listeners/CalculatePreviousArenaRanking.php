<?php

namespace App\Listeners;

use App\Events\ArenaRankingChanged;
use App\Events\PreviousArenaRankingChanged;
use App\Models\Arena;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\NoReturn;

class CalculatePreviousArenaRanking
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
    public function handle(PreviousArenaRankingChanged $event): void
    {
        $action = $event->action;
        $arena = $event->arena;
        $rankedAt = $event->rankedAt;

        $starOfMonth = $rankedAt->copy()->startOfMonth();
        $endOfMonth = $rankedAt->copy()->endOfMonth();

        $rankers = User::query()
            ->select(
                'users.*',
                DB::raw('RANK() OVER (PARTITION BY occurs_at ORDER BY revenues.turnover DESC) AS ranking')
            )
            ->leftJoin('revenues', 'revenues.user_id', 'users.id')
            ->whereBetween('occurs_at', [$starOfMonth, $endOfMonth])
            ->where('revenues.turnover', '>=', $arena->minimum_threshold)
            ->when(
                $nextArena = $this->nextArena($arena->minimum_threshold),
                fn(Builder $whenBuilder) => $whenBuilder->where('revenues.turnover', '<', $nextArena->minimum_threshold)
            )
            ->get();

        foreach ($rankers as $ranker) {
            $action->refreshArenaRanking($ranker, $arena, $rankedAt);
        }
    }

    private function nextArena(int $previousThreshold): ?Arena
    {
        return Arena::query()
            ->where('minimum_threshold', '>', $previousThreshold)
            ->orderBy('minimum_threshold')
            ->first();
    }
}
