<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'role'];

    protected $hidden = ['remember_token'];

    protected $casts = [
        'role' => 'string',
    ];

    // Relations
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function totalPoints(): int
    {
        return $this->predictions()->sum('points_earned');
    }

    public function predictionForMatch(string $matchId): ?Prediction
    {
        return $this->predictions()->where('fixture_id', $matchId)->first();
    }
}
