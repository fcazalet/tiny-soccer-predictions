<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiFootballService
{
    private string $baseUrl = 'https://v3.football.api-sports.io';
    private string $apiKey;
    private int $cacheTtlMinutes;

    public function __construct()
    {
        $this->apiKey = config('services.api_football.key');
        $this->cacheTtlMinutes = config('services.api_football.cache_ttl', 360); // 6h par défaut
    }

    // -------------------------------------------------------------------------
    // Public methods
    // -------------------------------------------------------------------------

    public function getFixturesByDate(string $date, int $leagueId, int $season): array
    {
        return $this->cachedGet("fixtures_date_{$date}_{$leagueId}_{$season}", [
            'date'   => $date,
            'league' => $leagueId,
            'season' => $season,
        ], endpoint: 'fixtures');
    }

    public function getFixtureById(int $fixtureId): array
    {
        return $this->cachedGet("fixture_{$fixtureId}", [
            'id' => $fixtureId,
        ], endpoint: 'fixtures');
    }

    public function getOddsByFixture(int $fixtureId): array
    {
        return $this->cachedGet("odds_fixture_{$fixtureId}", [
            'fixture' => $fixtureId,
            'market'  => 'Match Winner',
        ], endpoint: 'odds');
    }

    public function getOddsByDate(string $date, int $leagueId, int $season): array
    {
        return $this->cachedGet("odds_date_{$date}_{$leagueId}_{$season}", [
            'date'   => $date,
            'league' => $leagueId,
            'season' => $season,
            'market' => 'Match Winner',
        ], endpoint: 'odds');
    }

    public function getStandings(int $leagueId, int $season): array
    {
        return $this->cachedGet("standings_{$leagueId}_{$season}", [
            'league' => $leagueId,
            'season' => $season,
        ], endpoint: 'standings');
    }

    public function getRemainingRequests(): ?int
    {
        return Cache::get('api_football_remaining_requests');
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
            $response = Http::withHeaders([
                'x-apisports-key' => $this->apiKey,
            ])->get("{$this->baseUrl}/{$endpoint}", $params);

            if ($response->failed()) {
                Log::error("ApiFootball [{$endpoint}] HTTP error", [
                    'status' => $response->status(),
                    'params' => $params,
                ]);
                return [];
            }

            $this->trackRemainingRequests($response);

            $data = $response->json();

            if (!empty($data['errors'])) {
                Log::error("ApiFootball [{$endpoint}] API error", [
                    'errors' => $data['errors'],
                    'params' => $params,
                ]);
                return [];
            }

            return $data['response'] ?? [];

        } catch (\Exception $e) {
            Log::error("ApiFootball [{$endpoint}] Exception", [
                'message' => $e->getMessage(),
                'params'  => $params,
            ]);
            return [];
        }
    }

    private function trackRemainingRequests($response): void
    {
        $remaining = $response->header('x-ratelimit-requests-remaining');

        if ($remaining !== null) {
            Cache::put('api_football_remaining_requests', (int) $remaining, now()->addDay());

            if ((int) $remaining < 10) {
                Log::warning("ApiFootball : plus que {$remaining} requêtes disponibles aujourd'hui.");
            }
        }
    }
}