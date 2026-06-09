<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function storeJson(Request $request, Fixture $match)
    {
        if ($match->isLocked()) {
            return back()->with('error', 'Les pronostics sont fermés pour ce match.');
        }

        if ($match->isKnockout()){
            $validator = Validator::make($request->all(), [
                'home_score' => 'required|integer|min:0|max:20',
                'away_score' => 'required|integer|min:0|max:20',
                'predicted_winner' => 'required|in:home,away',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'home_score' => 'required|integer|min:0|max:20',
                'away_score' => 'required|integer|min:0|max:20',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        Prediction::updateOrCreate(
            ['user_id' => auth()->id(), 'fixture_id' => $match->id],
            $data
        );

        return response()->json([
            'success' => true
        ]);
    }
}
