<?php

namespace App\Actions;

use App\Events\ArenaRankingChanged;
use App\Events\PreviousArenaRankingChanged;
use App\Models\Arena;
use App\Models\ArenaRanking;
use App\Models\Revenue;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HandleArenaRanking
{
    public function processArenaChange(User $concerned, Arena $oldArena, Arena $newArena, Carbon $rankedAt): void
    {
        $concerned->arena()->associate($newArena)->save();

        if ($newArena->isNot($oldArena)) {
            ArenaRankingChanged::dispatch($this, $oldArena, $rankedAt);
        }

        ArenaRankingChanged::dispatch($this, $newArena, $rankedAt);
    }

    public function trackPreviousArenaRanking(User $concerned, Arena $newArena, Carbon $rankedAt): void
    {
        $oldArena = $concerned->arenaRankingOnDate($rankedAt);

        if ($oldArena && $oldArena->isNot($newArena)) {
            PreviousArenaRankingChanged::dispatch($this, $oldArena, $rankedAt);
        }

        PreviousArenaRankingChanged::dispatch($this, $newArena, $rankedAt);
    }

    public function refreshArenaRanking(User $ranker, Arena $arena, Carbon $rankedAt): void
    {
        if ($oldRanking = $ranker->arenaRankingOnDate($rankedAt)) {
            DB::table('arena_ranking')
                ->whereBetween('ranked_at', [$rankedAt->copy()->startOfMonth(), $rankedAt->copy()->endOfMonth()])
                ->where('user_id', $ranker->getKey())
                ->update([
                    'arena_id' => $arena->getKey(),
                    'ranked_at' => $rankedAt,
                    'ranking' => $ranker->ranking,
                    'turnover' => $ranker->revenueOnDate($rankedAt)->turnover,
                    'arena_evolution' => $this->calculateEvolution($oldRanking->pivot, $arena, $ranker->ranking),
                    'updated_at' => Carbon::now(),
                ]);
        } else {
            $ranker->arenaRankings()
                ->attach(
                    $arena->getKey(),
                    [
                        'ranked_at' => $rankedAt,
                        'ranking' => $ranker->ranking,
                        'turnover' => $ranker->revenueOnDate($rankedAt)->turnover
                    ]
                );
        }
    }

    private function calculateEvolution(ArenaRanking $oldRanking, Arena $newArena, int $newRanking): int
    {
        $oldArena = Arena::query()->find($oldRanking->arena_id);

        if ($newArena->is($oldArena)) {
            return $oldRanking->ranking <=> $newRanking;
        }

        return $newArena->minimum_threshold <=> $oldArena->minimum_threshold;
    }
}
