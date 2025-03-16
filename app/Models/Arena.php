<?php

namespace App\Models;

use App\QueryBuilders\ArenaQueryBuilder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Arena extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'label', 'minimum_threshold'
    ];

    public function newEloquentBuilder($query): ArenaQueryBuilder
    {
        return new ArenaQueryBuilder($query);
    }

    #region Relations
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function rankers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'arena_ranking')
            ->using(ArenaRanking::class)
            ->withPivot(['turnover', 'ranked_at', 'ranking', 'arena_evolution'])
            ->withTimestamps();
    }
    #endregion
}
