<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenue extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id', 'occurs_at', 'signed_amount', 'invoiced_amount', 'turnover'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurs_at' => 'datetime:Y-m',
            'signed_amount' => 'float',
            'invoiced_amount' => 'float',
            'turnover' => 'float',
        ];
    }

    #region Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    #endregion
}
