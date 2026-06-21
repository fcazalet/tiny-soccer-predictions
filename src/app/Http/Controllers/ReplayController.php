<?php

namespace App\Http\Controllers;
// Add this method to your existing DashboardController (or a new ReplayController).
// Route example: Route::get('/replay', [DashboardController::class, 'replay'])->name('replay');

use App\Models\Fixture;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReplayController extends Controller
{
    public function replay()
    {
        // 1. All distinct days that have at least one finished fixture (UTC midnight).
        $competitionDays = Fixture::whereNotNull('home_score')
            ->orderBy('played_at')
            ->get()
            ->map(fn ($f) => Carbon::parse($f->played_at)->utc()->startOfDay())
            ->unique(fn ($d) => $d->format('Y-m-d'))
            ->values();

        // 2. All players (ordered alphabetically; we'll sort by points per day in JS).
        $players = User::all();

        // 3. Build the snapshots: for each day, compute cumulative points + prediction count
        //    for every player up to (and including) that day at 23:59:59 UTC.
        $snapshotsByDay = [];

        foreach ($competitionDays as $day) {
            $cutoff = $day->copy()->endOfDay(); // 23:59:59 UTC of that day

            // Aggregate points per user for all finished fixtures up to this cutoff.
            $rows = DB::table('predictions')
                ->join('fixtures', 'predictions.fixture_id', '=', 'fixtures.id')
                ->join('users', 'predictions.user_id', '=', 'users.id')
                ->whereNotNull('fixtures.home_score')           // finished
                ->where('fixtures.played_at', '<=', $cutoff)   // up to this day
                ->groupBy('predictions.user_id', 'users.name')
                ->select([
                    'predictions.user_id as id',
                    'users.name as name',
                    DB::raw('SUM(predictions.points_earned) as total_points'),
                    DB::raw('COUNT(predictions.id) as total_predictions'),
                ])
                ->orderByDesc('total_points')
                ->get()
                ->map(fn ($r) => [
                    'id'                => $r->id,
                    'name'              => $r->name,
                    'total_points'      => (int) $r->total_points,
                    'total_predictions' => (int) $r->total_predictions,
                ])
                ->toArray();

            // Include players with 0 points who haven't appeared yet.
            $presentIds = collect($rows)->pluck('id')->toArray();
            foreach ($players as $p) {
                if (!in_array($p->id, $presentIds)) {
                    $rows[] = [
                        'id'                => $p->id,
                        'name'              => $p->name,
                        'total_points'      => 0,
                        'total_predictions' => 0,
                    ];
                }
            }

            // Sort by points desc, then alphabetically as tiebreaker.
            usort($rows, fn ($a, $b) =>
                $b['total_points'] <=> $a['total_points'] ?: $a['name'] <=> $b['name']
            );

            $snapshotsByDay[$day->format('Y-m-d')] = $rows;
        }

        return view('replay', compact('competitionDays', 'snapshotsByDay'));
    }
}