<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArenaRanking extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'arena_id', 'ranked_at', 'ranking', 'turnover', 'arena_evolution'
    ];

    protected function casts(): array
    {
        return [
            'ranked_at' => 'datetime:Y-m'
        ];
    }

    #region Relations
    public function arena(): BelongsTo
    {
        return $this->belongsTo(Arena::class);
    }
    #endregion
}
