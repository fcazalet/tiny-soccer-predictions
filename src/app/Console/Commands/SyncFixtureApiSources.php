<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Fixture;
use App\Models\FixtureApiSource;
use App\Services\ApiFootballService;

#[Signature('app:sync-fixture-api-sources')]
#[Description('Command description')]
class SyncFixtureApiSources extends Command
{
    protected $signature = 'fixtures:sync-api-sources
                            {--league=4 : ID de la ligue}
                            {--season=2024 : Saison}';

    protected $description = 'Associe les fixtures locales avec les IDs API Football';

    public function __construct(private ApiFootballService $apiFootball)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $leagueId = $this->option('league');
        $season   = $this->option('season');

        $this->info("Récupération des fixtures API Football...");

        // Récupère tous les matchs de la compétition
        $apiFixtures = $this->apiFootball->getFixturesByLeague($leagueId, $season);

        if (empty($apiFixtures)) {
            $this->error("Aucune donnée reçue de l'API.");
            return;
        }

        $this->info(count($apiFixtures) . " matchs récupérés.");

        $matched   = 0;
        $unmatched = [];

        foreach ($apiFixtures as $apiFixture) {
            $apiId    = $apiFixture['fixture']['id'];
            $homeTeam = $apiFixture['teams']['home']['name'];
            $awayTeam = $apiFixture['teams']['away']['name'];
            $date     = $apiFixture['fixture']['date'];

            // Cherche le match local par date + équipes
            $fixture = Fixture::with(['homeTeam', 'awayTeam'])
                ->whereDate('played_at', \Carbon\Carbon::parse($date)->toDateString())
                ->get()
                ->first(function ($f) use ($homeTeam, $awayTeam) {
                    return str_contains(strtolower($homeTeam), strtolower($f->homeTeam->name))
                        || str_contains(strtolower($f->homeTeam->name), strtolower($homeTeam));
                });

            if ($fixture) {
                FixtureApiSource::updateOrCreate(
                    ['fixture_id' => $fixture->id, 'source' => 'api_football'],
                    ['external_id' => $apiId]
                );
                $this->line("✅ {$homeTeam} vs {$awayTeam} → fixture #{$fixture->id}");
                $matched++;
            } else {
                $unmatched[] = "{$homeTeam} vs {$awayTeam} ({$date})";
            }
        }

        $this->info("\n{$matched} matchs associés.");

        if (!empty($unmatched)) {
            $this->warn(count($unmatched) . " matchs non trouvés :");
            foreach ($unmatched as $m) {
                $this->warn("  - {$m}");
            }
        }
    }
}
