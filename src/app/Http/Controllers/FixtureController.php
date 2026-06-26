<?php
namespace App\Http\Controllers;

use App\Services\ApiFootballService;
use App\Models\Fixture;

class FixtureController extends Controller
{
    public function __construct(private ApiFootballService $apiFootball) {}

    public function index()
    {
        $fixtures = $this->apiFootball->getFixturesByDate(
            date: now()->format('Y-m-d'),
            leagueId: 4,
            season: 2024,
        );

        // Vérifier les requêtes restantes
        $remaining = $this->apiFootball->getRemainingRequests();

        return view('fixtures.index', compact('fixtures', 'remaining'));
    }

    public function odds(Fixture $fixture): JsonResponse
    {
        $externalId = $fixture->apiFootballId();

        if (!$externalId) {
            return response()->json(['error' => 'No API source found'], 404);
        }

        $odds = $this->apiFootball->getOddsByFixture($externalId);

        return response()->json($odds);
    }
}