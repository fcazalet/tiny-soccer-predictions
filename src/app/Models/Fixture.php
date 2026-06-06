<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Fixture extends Model
{
//    use HasUuids;

    protected $fillable = [
        'home_team_id', 'away_team_id',
        'phase', 'played_at',
        'home_score', 'away_score',
    ];

    protected $casts = [
        'played_at'  => 'datetime',
        'home_score' => 'integer',
        'away_score' => 'integer',
    ];

    public function getLocalPlayedAt(): Carbon
    {
        return $this->played_at->setTimezone(config('app.display_timezone'));
    }

    // Relations
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    // State helpers
    public function isFinished(): bool
    {
        return $this->home_score !== null && $this->away_score !== null;
    }

    public function isLocked(): bool
    {
        // Pronostics verrouillés 5 minutes avant le coup d'envoi
        return now()->gte($this->played_at->subMinutes(5));
    }

    public function winner(): ?string
    {
        if (! $this->isFinished()) return null;

        return match(true) {
            $this->home_score > $this->away_score => 'home',
            $this->away_score > $this->home_score => 'away',
            default                               => 'draw',
        };
    }

    // Calcul des points pour un pronostic donné
    // Règle : 3 pts score exact / 2 pts bon vainqueur + bonne diff / 1 pt bon vainqueur / 0 sinon
    public function computePoints(int $predictedHome, int $predictedAway): int
    {
        if (! $this->isFinished()) return 0;

        // Score exact
        if ($predictedHome === $this->home_score && $predictedAway === $this->away_score) {
            return 3;
        }

        $predictedWinner = match(true) {
            $predictedHome > $predictedAway => 'home',
            $predictedAway > $predictedHome => 'away',
            default                         => 'draw',
        };

        if ($predictedWinner !== $this->winner()) {
            return 0;
        }

        // Bon vainqueur + bonne différence de buts
        $predictedDiff = $predictedHome - $predictedAway;
        $actualDiff    = $this->home_score - $this->away_score;

        return $predictedDiff === $actualDiff ? 2 : 1;
    }

    // Recalcule et sauvegarde les points de tous les pronostics de ce match
    public function scoreAllPredictions(): void
    {
        $this->predictions->each(function (Prediction $prediction) {
            $prediction->update([
                'points_earned' => $this->computePoints(
                    $prediction->home_score,
                    $prediction->away_score,
                ),
            ]);
        });
    }

    public function phaseLabel(): string
    {
        $label = __('app.phase_' . $this->phase);
        return $label;
    }
}
