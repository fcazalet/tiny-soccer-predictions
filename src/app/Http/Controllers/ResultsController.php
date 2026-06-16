<?php

namespace App\Http\Controllers;

use App\Models\Fixture;

class ResultsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $phase = request('phase');

        $allFixtures = Fixture::whereHas('predictions', fn($q) => $q->where('user_id', $user->id))->get();
        $phases = $allFixtures->map(fn($m) => $m->phase)->unique()->values();

        // sum of points_earned for the current user
        $totalPointsEarned = $user->predictions()->sum('points_earned');

        $userPredictions = $user->predictions()
            ->whereIn('fixture_id', $allFixtures->pluck('id'))
            ->when($phase, fn($q) => $q->whereHas('fixture', fn($q) => $q->where('phase', $phase)))
            ->get()
            ->sortBy(fn($p) => $p->fixture->played_at)
            ->keyBy('fixture_id');

        $totalFixturesCount = Fixture::count();

        return view('results', compact(
            'phases',
            'allFixtures',
            'totalPointsEarned',
            'userPredictions',
            'totalFixturesCount',
        ));
    }
}
