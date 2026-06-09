<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
//    use HasUuids;

    protected $fillable = [
        'user_id', 'fixture_id',
        'home_score', 'away_score',
        'predicted_winner', // 'home' | 'away' | null — mandatory on knockout phases
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

    /**
     * Libellé complet du pronostic (score + vainqueur si phase éliminatoire).
     */
    public function fullLabel(): string
    {
        $label = $this->scoreLabel();

        if ($this->predicted_winner !== null) {
            // On résout le nom de l'équipe via la relation du match
            $team = $this->predicted_winner === 'home'
                ? $this->match->homeTeam->displayName()
                : $this->match->awayTeam->displayName();

            $label .= " · {$team}";
        }

        return $label;
    }
}
