<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
//    use HasUuids;

    protected $fillable = [
        'user_id', 'fixture_id',
        'home_score', 'away_score',
        'points_earned',
    ];

    protected $casts = [
        'home_score'    => 'integer',
        'away_score'    => 'integer',
        'points_earned' => 'integer',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->points_earned === null;
    }

    public function scoreLabel(): string
    {
        return "{$this->home_score} – {$this->away_score}";
    }
}
