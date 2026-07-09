<?php
namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $playedFixtures = Fixture::whereNotNull('home_score')->get();
        $playedFixtureIds = $playedFixtures->pluck('id');

        // 1. Top "bons scores" (pronostics exacts) par joueur
        $exactScoresByPlayer = Prediction::query()
            ->whereIn('fixture_id', $playedFixtureIds)
            ->whereHas('fixture', fn($q) => $q->whereColumn('predictions.home_score', 'fixtures.home_score')
                                               ->whereColumn('predictions.away_score', 'fixtures.away_score'))
            ->select('user_id', DB::raw('count(*) as exact_count'))
            ->groupBy('user_id')
            ->orderByDesc('exact_count')
            ->with('user')
            ->take(5)
            ->get();

        // 2. Classement des joueurs par phase (total points)
        $phases = $playedFixtures->pluck('phase')->unique()->values();

        $pointsByPlayerAndPhase = Prediction::query()
            ->join('fixtures', 'fixtures.id', '=', 'predictions.fixture_id')
            ->join('users', 'users.id', '=', 'predictions.user_id')
            ->whereIn('predictions.fixture_id', $playedFixtureIds)
            ->select('users.id as user_id', 'users.name', 'fixtures.phase',
                     DB::raw('sum(predictions.points_earned) as total_points'))
            ->groupBy('users.id', 'users.name', 'fixtures.phase')
            ->get()
            ->groupBy('phase');

        // 3. Matchs avec le plus de scores exacts
        $mostExactFixtures = Fixture::query()
            ->whereIn('id', $playedFixtureIds)
            ->withCount(['predictions as exact_count' => function ($q) {
                $q->whereColumn('predictions.home_score', 'fixtures.home_score')
                  ->whereColumn('predictions.away_score', 'fixtures.away_score');
            }])
            ->with(['homeTeam', 'awayTeam'])
            ->orderByDesc('exact_count')
            ->take(5)
            ->get()
            ->filter(fn($f) => $f->exact_count > 0);

        // 4. Matchs où personne n'a trouvé le bon vainqueur
        $noWinnerFoundFixtures = Fixture::query()
            ->whereIn('id', $playedFixtureIds)
            ->whereNotNull('winner')
            ->get()
            ->filter(function ($fixture) {
                $totalPredictions = $fixture->predictions()->count();
                if ($totalPredictions === 0) return false;

                $correctWinnerCount = $fixture->predictions()
                    ->where('predicted_winner', $fixture->winner)
                    ->count();

                return $correctWinnerCount === 0;
            });

        // 5. Joueur le plus "audacieux" = paris les plus souvent contre les autres
        $mostAudaciousPlayers = $this->computeMostAudaciousPlayers($playedFixtureIds);

        return view('stats.index', compact(
            'exactScoresByPlayer',
            'pointsByPlayerAndPhase',
            'phases',
            'mostExactFixtures',
            'noWinnerFoundFixtures',
            'mostAudaciousPlayers',
        ));
    }

    private function predictedOutcome($homeScore, $awayScore): string
    {
        if ($homeScore > $awayScore) return 'home';
        if ($homeScore < $awayScore) return 'away';
        return 'draw';
    }

    private function computeMostAudaciousPlayers($playedFixtureIds)
    {
        $contrarianCounts = []; // [user_id => count]

        $predictionsByFixture = Prediction::query()
            ->whereIn('fixture_id', $playedFixtureIds)
            ->with('user')
            ->get()
            ->groupBy('fixture_id');

        foreach ($predictionsByFixture as $fixtureId => $predictions) {
            if ($predictions->count() < 3) {
                continue; // pas assez de votants pour parler de "majorité"
            }

            $outcomes = $predictions->map(function ($p) {
                return $this->predictedOutcome($p->home_score, $p->away_score);
            });

            $tally = $outcomes->countBy(); // ex: ['home' => 5, 'draw' => 2, 'away' => 1]
            $maxCount = $tally->max();
            $topOutcomes = $tally->filter(fn($count) => $count === $maxCount);

            if ($topOutcomes->count() > 1) {
                continue; // égalité parfaite, pas de majorité claire
            }

            $majorityOutcome = $topOutcomes->keys()->first();

            foreach ($predictions as $p) {
                $outcome = $this->predictedOutcome($p->home_score, $p->away_score);
                if ($outcome !== $majorityOutcome) {
                    $contrarianCounts[$p->user_id] = ($contrarianCounts[$p->user_id] ?? 0) + 1;
                }
            }
        }

        arsort($contrarianCounts);

        $topUserIds = array_slice(array_keys($contrarianCounts), 0, 5, true);
        $users = User::whereIn('id', $topUserIds)->get()->keyBy('id');

        return collect($topUserIds)->map(fn($userId) => [
            'user' => $users[$userId],
            'contrarian_count' => $contrarianCounts[$userId],
        ])->values();
    }
}