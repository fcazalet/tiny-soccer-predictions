<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Prediction;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function store(Request $request, Fixture $match)
    {
        if ($match->isLocked()) {
            return back()->with('error', 'Les pronostics sont fermés pour ce match.');
        }

        $data = $request->validate([
            'home_score' => 'required|integer|min:0|max:20',
            'away_score' => 'required|integer|min:0|max:20',
        ]);

        Prediction::updateOrCreate(
            ['user_id' => auth()->id(), 'fixture_id' => $match->id],
            $data
        );

        return back()->with('success', 'Pronostic enregistré !');
    }
}
