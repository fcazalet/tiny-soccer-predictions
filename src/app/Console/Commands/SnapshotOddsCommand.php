<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fixture;
use App\Services\ApiOddsService;
use Illuminate\Support\Facades\Log;

class SnapshotOddsCommand extends Command
{
    protected $signature = 'fixtures:snapshot-odds';

    protected $description = 'Populate missing odds';

    public function __construct(private ApiOddsService $oddsApi)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // one request for all fixtures
        $allOdds = $this->oddsApi->getUpcomingOdds();
        if (empty($allOdds)) return;

        $upcoming = Fixture::whereNull('home_score')
            ->where('played_at', '>', now())
            ->with(['homeTeam', 'awayTeam'])
            ->get();
        
        if (empty($upcoming)) {
                $this->info("No upcoming fixtures");
                Log::info("No upcoming fixtures", []);
        }

        foreach ($upcoming as $fixture) {
            $raw = collect($allOdds)->first(
                fn($o) =>
                strtolower($o['home_team']) === strtolower($fixture->homeTeam->oddsApiName()) &&
                    strtolower($o['away_team']) === strtolower($fixture->awayTeam->oddsApiName())
            );

            if ($raw) {
                $fixture->update(['odds' => $this->oddsApi->parseAverageOdds($raw)]);
                $this->info("✅ {$fixture->id} odds updated ({$fixture->homeTeam->oddsApiName()}-{$fixture->awayTeam->oddsApiName()})");
                Log::info("Odds updated", [
                    'fixture_id' => $fixture->id,
                    'home_team'=>$fixture->homeTeam->oddsApiName(),
                    'away_team'=>$fixture->awayTeam->oddsApiName(),
                ]);
            } else {
                $this->error("❌ {$fixture->id} odds not found ({$fixture->homeTeam->oddsApiName()}-{$fixture->awayTeam->oddsApiName()})");
                Log::error("Odds not found", [
                    'fixture_id' => $fixture->id,
                    'home_team'=>$fixture->homeTeam->oddsApiName(),
                    'away_team'=>$fixture->awayTeam->oddsApiName(),
                ]);
            }
        }
    }
}
