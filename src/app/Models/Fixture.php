<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Fixture extends Model
{

    // Knockout stages where the winner must be specified separately from the score.
    const KNOCKOUT_PHASES = ['r32', 'r16', 'qf', 'sf', 'final'];

    protected $fillable = [
        'home_team_id', 'away_team_id',
        'phase', 'played_at',
        'home_score', 'away_score',
        'winner', // Actual winner (may differ from the score due to extra time or a penalty shootout).
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
    public function isKnockout(): bool
    {
        return in_array($this->phase, self::KNOCKOUT_PHASES);
    }

    public function isFinished(): bool
    {
        if (! ($this->home_score !== null && $this->away_score !== null)) {
            return false;
        }

        // In knockout stages, a match is considered "completed" (eligible for scoring)
        // only when the actual winner has also been specified.
        if ($this->isKnockout()) {
            return $this->winner !== null;
        }

        return true;
    }

    public function isLocked(): bool
    {
        // Pronostics verrouillés 5 minutes avant le coup d'envoi
        return now()->gte($this->played_at->subMinutes(5));
    }

    /**
     * Winner based on the 90-minute score (used for group-stage matches).
     * For knockout stages, use $this->winner (entered manually).
     */
    public function winnerByScore(): ?string
    {
        if ($this->home_score === null || $this->away_score === null) {
            return null;
        }

        return match(true) {
            $this->home_score > $this->away_score => 'home',
            $this->away_score > $this->home_score => 'away',
            default                               => 'draw',
        };
    }

    // -----------------------------------------------------------------------
    // Points
    // -----------------------------------------------------------------------

    /**
     * Calculates the points for a given prediction.
     *
     * Common rules (all stages, based on 90-minute score):
     *   3 pts : exact score
     *   2 pts : correct result (winner or draw) + correct goal difference
     *   1 pt  : correct result (winner or draw)
     *   0 pt  : wrong result
     *
     * Knockout bonus (round of 16, quarter-finals, semi-finals, final):
     *   +1 pt if correct qualified team (predicted_winner === $this->winner)
     *   The actual qualified team may differ from the 90-minute result (extra time/penalties).
     */
    public function computePoints(int $predictedHome, int $predictedAway, ?string $predictedWinner = null): int
    {
        if (! $this->isFinished()) {
            return 0;
        }

        $points = $this->computeScorePoints($predictedHome, $predictedAway);

        // Bonus qualifié en phase éliminatoire
        if ($this->isKnockout() && $predictedWinner !== null && $predictedWinner === $this->winner) {
            $points += 1;
        }

        return $points;
    }

    /**
     * Points liés au score à 90 min — identiques en toutes phases.
     */
    private function computeScorePoints(int $predictedHome, int $predictedAway): int
    {
        // Score exact
        if ($predictedHome === $this->home_score && $predictedAway === $this->away_score) {
            return 3;
        }

        $predictedResult = match(true) {
            $predictedHome > $predictedAway => 'home',
            $predictedAway > $predictedHome => 'away',
            default                         => 'draw',
        };

        // Right result
        if ($predictedResult !== $this->winnerByScore()) {
            return 0;
        }

        // Right result + right goals diff
        $predictedDiff = $predictedHome - $predictedAway;
        $actualDiff    = $this->home_score - $this->away_score;

        return $predictedDiff === $actualDiff ? 2 : 1;
    }

    // -----------------------------------------------------------------------
    // Bulk scoring
    // -----------------------------------------------------------------------

    /**
     * Recalculates and saves the points for all predictions of this match.
     */
    public function scoreAllPredictions(): void
    {
        $this->predictions->each(function (Prediction $prediction) {
            $prediction->update([
                'points_earned' => $this->computePoints(
                    $prediction->home_score,
                    $prediction->away_score,
                    $prediction->predicted_winner,
                ),
            ]);
        });
    }

    // -----------------------------------------------------------------------
    // Labels
    // -----------------------------------------------------------------------

    public function phaseLabel(): string
    {
        return __('app.phase_' . $this->phase);
    }

    public function apiSources(): HasMany
    {
        return $this->hasMany(FixtureApiSource::class);
    }

    public function apiFootballId(): ?string
    {
        return $this->apiSources
            ->firstWhere('source', 'api_football')
            ?->external_id;
    }
}
