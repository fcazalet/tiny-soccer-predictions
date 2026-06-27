<?php
namespace App\Http\Controllers;

use App\Services\ApiOddsService;
use App\Models\Fixture;
use Illuminate\Http\JsonResponse;

class FixtureController extends Controller
{
    public function __construct(private ApiOddsService $oddsApi) {}

    // public function index()
    // {
    //     return view('fixtures.index', compact('fixtures', 'remaining'));
    // }

    public function odds(Fixture $fixture): JsonResponse
    {
       $fixture->loadMissing(['homeTeam', 'awayTeam']);

        $odds = $this->oddsApi->getOddsForFixture(
            $fixture->homeTeam->oddsApiName(),
            $fixture->awayTeam->oddsApiName(),
        );

        if (!$odds) {
            return response()->json(['error' => 'Aucune cote disponible'], 404);
        }

        return response()->json($this->oddsApi->parseAverageOdds($odds));
    }
}