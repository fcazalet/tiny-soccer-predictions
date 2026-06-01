<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Matchs à venir non verrouillés (pronostics encore possibles)
        $upcomingMatches = Fixture::with(['homeTeam', 'awayTeam'])
            ->whereNull('home_score')
            ->where('played_at', '>', now()->subMinutes(5))
            ->orderBy('played_at')
            ->get();

        // Matchs terminés avec résultats
        $finishedMatches = Fixture::with(['homeTeam', 'awayTeam', 'predictions' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
            ->whereNotNull('home_score')
            ->orderByDesc('played_at')
            ->take(10)
            ->get();

        // Classement
        $leaderboard = User::where('role', 'player')
            ->withSum('predictions', 'points_earned')
            ->orderByDesc('predictions_sum_points_earned')
            ->get();

        // Pronostics déjà soumis par l'utilisateur pour les matchs à venir
        $userPredictions = $user->predictions()
            ->whereIn('fixture_id', $upcomingMatches->pluck('id'))
            ->get()
            ->keyBy('fixture_id');

        return view('dashboard', compact(
            'upcomingMatches',
            'finishedMatches',
            'leaderboard',
            'userPredictions'
        ));
    }
}
