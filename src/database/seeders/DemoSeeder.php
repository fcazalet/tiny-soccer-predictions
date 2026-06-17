<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fixture;
use App\Models\Prediction;

class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the Demo part.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate([
            'name'  => 'Demo Admin',
            'email' => 'admin@demo.com',
            'role'  => 'admin',
        ]);

        $nombreDeJoueurs = 10;
        $players =  [];
        for ($i = 1; $i <= $nombreDeJoueurs; $i++) {
            $players[] = User::firstOrCreate([
                'email' => "player{$i}@demo.com",
            ], [
                'name' => "player{$i}",
                'role' => 'player',
            ]);
        }

        // Init predictions
        $predictions = [
            'A' => [['MX', '🇲🇽'], ['ZA', '🇿🇦'], ['KR', '🇰🇷'], ['CZ', '🇨🇿']],
            'B' => [['CA', '🇨🇦'], ['BA', '🇧🇦'], ['QA', '🇶🇦'], ['CH', '🇨🇭']],
            'C' => [['BR', '🇧🇷'], ['MA', '🇲🇦'], ['HT', '🇭🇹'], ['GB-SCT', '🏴󠁧󠁢󠁳󠁣󠁴󠁿']],
            'D' => [['US', '🇺🇸'], ['PY', '🇵🇾'], ['AU', '🇦🇺'], ['TR', '🇹🇷']],
            'E' => [['DE', '🇩🇪'], ['CW', '🇨🇼'], ['CI', '🇨🇮'], ['EC', '🇪🇨']],
            'F' => [['NL', '🇳🇱'], ['JP', '🇯🇵'], ['SE', '🇸🇪'], ['TN', '🇹🇳']],
            'G' => [['BE', '🇧🇪'], ['EG', '🇪🇬'], ['IR', '🇮🇷'], ['NZ', '🇳🇿']],
            'H' => [['ES', '🇪🇸'], ['CV', '🇨🇻'], ['SA', '🇸🇦'], ['UY', '🇺🇾']],
            'I' => [['FR', '🇫🇷'], ['SN', '🇸🇳'], ['IQ', '🇮🇶'], ['NO', '🇳🇴']],
            'J' => [['AR', '🇦🇷'], ['DZ', '🇩🇿'], ['AT', '🇦🇹'], ['JO', '🇯🇴']],
            'K' => [['PT', '🇵🇹'], ['CD', '🇨🇩'], ['UZ', '🇺🇿'], ['CO', '🇨🇴']],
            'L' => [['GB-ENG', '🏴󠁧󠁢󠁥󠁮󠁧󠁿'], ['HR', '🇭🇷'], ['GH', '🇬🇭'], ['PA', '🇵🇦']],
        ];

        $fixtures = Fixture::all();

        foreach ($fixtures as $fixture) {
            foreach ($players as $player) {
                $winners = ['home', 'away'];
                $prediction = Prediction::create([
                    'user_id' => $player->id,
                    'fixture_id' => $fixture->id,
                    'home_score' => random_int(0, 4),
                    'away_score' => random_int(0, 4),
                    'predicted_winner' => $fixture->isKnockout() ? $winners[random_int(0, 1)] : null,
                ]);
            }
        }
    }
}
