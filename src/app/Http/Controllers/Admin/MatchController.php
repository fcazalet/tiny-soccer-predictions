<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $matches = Fixture::with(['homeTeam', 'awayTeam'])
            ->orderBy('played_at')
            ->get()
            ->groupBy('phase');

        return view('admin.matches.index', compact('matches'));
    }

    public function create()
    {
        $teams = Team::with('group')->orderBy('name')->get();
        return view('admin.matches.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'home_team_id' => 'required|integer|exists:teams,id',
            'away_team_id' => 'required|integer|exists:teams,id|different:home_team_id',
            'phase'        => 'required|in:group,r32,r16,qf,sf,final',
            'played_at'    => 'required|date',
        ]);

        if(!config('app.demo_mode')){
            Fixture::create($data);
        }

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match créé.');
    }

    public function editScore(Fixture $match)
    {
        $match->load(['homeTeam', 'awayTeam']);
        return view('admin.matches.score', compact('match'));
    }

    public function updateScore(Request $request, Fixture $match)
    {
        try{
        if ($match->isKnockout()){
            $data = $request->validate([
                'home_score' => 'required|integer|min:0|max:20',
                'away_score' => 'required|integer|min:0|max:20',
                'winner' => 'required|in:home,away',
            ]);
        } else {
            $data = $request->validate([
                'home_score' => 'required|integer|min:0|max:20',
                'away_score' => 'required|integer|min:0|max:20',
            ]);
        }
        }
        catch (\Exception $e){
            var_dump($e->getMessage());
            die();
        }

        $match->update($data);

        // Recalcule les points de tous les pronostics
        $match->load('predictions');
        $match->scoreAllPredictions();

        return redirect()->route('admin.matches.index')
            ->with('success', 'Score enregistré et points calculés.');
    }
}
