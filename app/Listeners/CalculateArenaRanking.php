<?php

namespace App\Listeners;

use App\Events\ArenaRankingChanged;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class CalculateArenaRanking
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
    public function handle(ArenaRankingChanged $event): void
    {
        $action = $event->action;
        $arena = $event->arena;
        $rankedAt = $event->rankedAt;

        $starOfMonth = $rankedAt->copy()->startOfMonth();
        $endOfMonth = $rankedAt->copy()->endOfMonth();

        $rankers = User::query()
            ->select(
                'users.*',
                DB::raw('RANK() OVER (PARTITION BY users.arena_id ORDER BY revenues.turnover DESC) AS ranking')
            )
            ->leftJoin('revenues', 'revenues.user_id', 'users.id')
            ->whereBetween('occurs_at', [$starOfMonth, $endOfMonth])
            ->where('users.arena_id', $arena->getKey())
            ->get();

        foreach ($rankers as $ranker) {
            $action->refreshArenaRanking($ranker, $arena, $rankedAt);
        }
    }
}
