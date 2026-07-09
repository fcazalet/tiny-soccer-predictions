<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Fixture;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    public function predictions(User $player)
    {
        $phase = request('phase');

        // Fixtures déjà jouées (score renseigné) sur lesquelles ce joueur a pronostiqué
        $playedFixtures = Fixture::whereNotNull('home_score')->get();

        $phases = $playedFixtures->map(fn($f) => $f->phase)->unique()->values();

        $totalPointsEarned = $player->predictions()
            ->whereIn('fixture_id', $playedFixtures->pluck('id'))
            ->sum('points_earned');

        $predictions = $player->predictions()
            ->whereIn('fixture_id', $playedFixtures->pluck('id'))
            ->when($phase, fn($q) => $q->whereHas('fixture', fn($q) => $q->where('phase', $phase)))
            ->get()
            ->sortByDesc(fn($p) => $p->fixture->played_at);

        return view('players.predictions', compact(
            'player',
            'phases',
            'predictions',
            'totalPointsEarned',
        ));
    }
}