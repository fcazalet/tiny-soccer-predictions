<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiOddsService
{
    private string $baseUrl = 'https://api.the-odds-api.com/v4';
    private string $apiKey;
    private int $cacheTtlMinutes;
    private string $sport = 'soccer_fifa_world_cup';

    public function __construct()
    {
        $this->apiKey          = config('services.odds_api.key');
        $this->cacheTtlMinutes = config('services.odds_api.cache_ttl', 360);
    }

    // -------------------------------------------------------------------------
    // Public methods
    // -------------------------------------------------------------------------

    public function getUpcomingOdds(string $market = 'h2h'): array
    {
        return $this->cachedGet("odds_upcoming_{$market}", [
            'apiKey'           => $this->apiKey,
            'regions'          => 'eu',
            'markets'          => $market,
            'oddsFormat'       => 'decimal',
            'dateFormat'       => 'iso',
        ], endpoint: "sports/{$this->sport}/odds");
    }

    public function getOddsForFixture(string $homeTeamOddsName, string $awayTeamOddsName): ?array
    {
        $allOdds = $this->getUpcomingOdds();
        $result = collect($allOdds)->first(function ($odd) use ($homeTeamOddsName, $awayTeamOddsName) {
            return strtolower($odd['home_team']) === strtolower($homeTeamOddsName)
                && strtolower($odd['away_team']) === strtolower($awayTeamOddsName);
        });
        return $result;
    }

    public function parseAverageOdds(array $fixture): array
    {
        $totals = ['home' => [], 'draw' => [], 'away' => []];

        $homeName = $fixture['home_team'];
        $awayName = $fixture['away_team'];

        foreach ($fixture['bookmakers'] as $bookmaker) {
            $market = collect($bookmaker['markets'])->firstWhere('key', 'h2h');
            if (!$market) continue;

            foreach ($market['outcomes'] as $outcome) {
                if ($outcome['name'] === $homeName)  $totals['home'][] = $outcome['price'];
                elseif ($outcome['name'] === $awayName) $totals['away'][] = $outcome['price'];
                elseif ($outcome['name'] === 'Draw')    $totals['draw'][] = $outcome['price'];
            }
        }

        $avg = fn(array $values) => count($values)
            ? round(array_sum($values) / count($values), 2)
            : null;

        $homeOdd = $avg($totals['home']);
        $drawOdd = $avg($totals['draw']);
        $awayOdd = $avg($totals['away']);
        $invHome = 1 / $homeOdd;
        $invDraw = 1 / $drawOdd;
        $invAway = 1 / $awayOdd;
        $total   = $invHome + $invDraw + $invAway;

        return [
            'home_team' => $homeName,
            'away_team' => $awayName,
            'odds'      => [
                'home' => $homeOdd,
                'draw' => $drawOdd,
                'away' => $awayOdd,
            ],
            'probabilities' => [
                'home' => round($invHome / $total * 100),
                'draw' => round($invDraw / $total * 100),
                'away' => round($invAway / $total * 100),
            ],
            'bookmakers_count' => count($fixture['bookmakers']),
        ];
    }

    public function getRemainingRequests(): ?int
    {
        return Cache::get('odds_api_remaining_requests');
    }

    // -------------------------------------------------------------------------
    // Private methods
    // -------------------------------------------------------------------------

    private function cachedGet(string $cacheKey, array $params, string $endpoint): array
    {
        return Cache::remember($cacheKey, now()->addMinutes($this->cacheTtlMinutes), function () use ($params, $endpoint) {
            return $this->get($endpoint, $params);
        });
    }

    private function get(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$endpoint}", $params);

            if ($response->failed()) {
                Log::error("OddsApi [{$endpoint}] HTTP error", [
                    'status' => $response->status(),
                    'params' => $params,
                ]);
                return [];
            }

            $this->trackRemainingRequests($response);

            return $response->json() ?? [];

        } catch (\Exception $e) {
            Log::error("OddsApi [{$endpoint}] Exception", [
                'message' => $e->getMessage(),
                'params'  => $params,
            ]);
            return [];
        }
    }

    private function trackRemainingRequests($response): void
    {
        $remaining = $response->header('x-requests-remaining');

        if ($remaining !== null) {
            Cache::put('odds_api_remaining_requests', (int) $remaining, now()->addDay());

            if ((int) $remaining < 20) {
                Log::warning("OddsApi : plus que {$remaining} requêtes disponibles ce mois-ci.");
            }
        }
    }
}