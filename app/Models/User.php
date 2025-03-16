<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\QueryBuilders\UserQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, HasUlids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'arena_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function newEloquentBuilder($query): UserQueryBuilder
    {
        return new UserQueryBuilder($query);
    }

    #region Relations
    public function arena(): BelongsTo
    {
        return $this->belongsTo(Arena::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function revenueOnDate(Carbon $date): ?Revenue
    {
        return $this->revenues()
            ->where(
                fn(Builder $whereBuilder) => $whereBuilder->whereYear('occurs_at', $date->year)
                    ->whereMonth('occurs_at', $date->month)
            )
            ->first();
    }

    public function currentRevenue(): ?Revenue
    {
        return $this->revenueOnDate(Carbon::now());
    }

    public function arenaRankings(): BelongsToMany
    {
        return $this->belongsToMany(Arena::class, 'arena_ranking')
            ->using(ArenaRanking::class)
            ->withPivot(['turnover', 'ranked_at', 'ranking', 'arena_evolution'])
            ->withTimestamps();
    }

    public function arenaRankingOnDate(Carbon $date): ?Arena
    {
        return $this->arenaRankings()
            ->where(
                fn(Builder $whereBuilder) => $whereBuilder->whereYear('ranked_at', $date->year)
                    ->whereMonth('ranked_at', $date->month)
            )
            ->latest('ranked_at')
            ->first();
    }

    public function currentArenaRanking(): ?Arena
    {
        return $this->arenaRankingOnDate(Carbon::now());
    }
    #endregion
}
