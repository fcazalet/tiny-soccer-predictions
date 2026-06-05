<?php

namespace Database\Seeders;

use App\Models\Fixture;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Team;

class WorldCup2026Seeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate([
            'name'  => 'Admin',
            'email' => 'admin@tinysp.local',
            'role'  => 'admin',
        ]);

        // Groupes et équipes Coupe du Monde 2026
        $groups = [
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

        foreach ($groups as $letter => $teams) {
            $group = Group::create(['name' => $letter]);
            foreach ($teams as [$name, $flag]) {
                Team::create([
                    'group_id'   => $group->id,
                    'name'       => $name,
                    'flag_emoji' => $flag,
                ]);
            }
        }

        // ---------------------------------------------------------------
        // Phase de groupes – 72 matchs
        // Correspondance nom JSON → code ISO (= champ 'name' en BDD)
        // Les équipes "UEFA/IC Path X winner" sont mappées sur l'équipe
        // correspondante telle que configurée dans le seeder.
        // ---------------------------------------------------------------
        $teamNameToCode = [
            // Groupe A
            'Mexico'             => 'MX',
            'South Africa'       => 'ZA',
            'South Korea'        => 'KR',
            'UEFA Path D winner' => 'CZ',   // République tchèque
            // Groupe B
            'Canada'             => 'CA',
            'UEFA Path A winner' => 'BA',   // Bosnie-Herzégovine
            'Qatar'              => 'QA',
            'Switzerland'        => 'CH',
            // Groupe C
            'Brazil'             => 'BR',
            'Morocco'            => 'MA',
            'Haiti'              => 'HT',
            'Scotland'           => 'GB-SCT',
            // Groupe D
            'USA'                => 'US',
            'Paraguay'           => 'PY',
            'Australia'          => 'AU',
            'UEFA Path C winner' => 'TR',   // Turquie
            // Groupe E
            'Germany'            => 'DE',
            'Curaçao'            => 'CW',
            'Ivory Coast'        => 'CI',
            'Ecuador'            => 'EC',
            // Groupe F
            'Netherlands'        => 'NL',
            'Japan'              => 'JP',
            'UEFA Path B winner' => 'SE',   // Suède
            'Tunisia'            => 'TN',
            // Groupe G
            'Belgium'            => 'BE',
            'Egypt'              => 'EG',
            'Iran'               => 'IR',
            'New Zealand'        => 'NZ',
            // Groupe H
            'Spain'              => 'ES',
            'Cape Verde'         => 'CV',
            'Saudi Arabia'       => 'SA',
            'Uruguay'            => 'UY',
            // Groupe I
            'France'             => 'FR',
            'Senegal'            => 'SN',
            'IC Path 2 winner'   => 'IQ',   // Irak
            'Norway'             => 'NO',
            // Groupe J
            'Argentina'          => 'AR',
            'Algeria'            => 'DZ',
            'Austria'            => 'AT',
            'Jordan'             => 'JO',
            // Groupe K
            'Portugal'           => 'PT',
            'IC Path 1 winner'   => 'CD',   // RD Congo
            'Uzbekistan'         => 'UZ',
            'Colombia'           => 'CO',
            // Groupe L
            'England'            => 'GB-ENG',
            'Croatia'            => 'HR',
            'Ghana'              => 'GH',
            'Panama'             => 'PA',
        ];

        // Index des équipes par code ISO (= champ 'name' en BDD)
        $teamIndex = Team::all()->keyBy('name');

        // Matchs de la phase de groupes, extraits du JSON openfootball
        // (source : https://github.com/openfootball/worldcup.json)
        $groupMatches = [
            // --- Groupe A ---
            ['round' => 'Matchday 1',  'date' => '2026-06-11', 'time' => '13:00 UTC-6', 'team1' => 'Mexico',             'team2' => 'South Africa'],
            ['round' => 'Matchday 1',  'date' => '2026-06-11', 'time' => '20:00 UTC-6', 'team1' => 'South Korea',         'team2' => 'UEFA Path D winner'],
            ['round' => 'Matchday 8',  'date' => '2026-06-18', 'time' => '12:00 UTC-4', 'team1' => 'UEFA Path D winner',  'team2' => 'South Africa'],
            ['round' => 'Matchday 8',  'date' => '2026-06-18', 'time' => '19:00 UTC-6', 'team1' => 'Mexico',             'team2' => 'South Korea'],
            ['round' => 'Matchday 14', 'date' => '2026-06-24', 'time' => '19:00 UTC-6', 'team1' => 'UEFA Path D winner',  'team2' => 'Mexico'],
            ['round' => 'Matchday 14', 'date' => '2026-06-24', 'time' => '19:00 UTC-6', 'team1' => 'South Africa',        'team2' => 'South Korea'],
            // --- Groupe B ---
            ['round' => 'Matchday 2',  'date' => '2026-06-12', 'time' => '15:00 UTC-4', 'team1' => 'Canada',             'team2' => 'UEFA Path A winner'],
            ['round' => 'Matchday 3',  'date' => '2026-06-13', 'time' => '12:00 UTC-7', 'team1' => 'Qatar',              'team2' => 'Switzerland'],
            ['round' => 'Matchday 8',  'date' => '2026-06-18', 'time' => '12:00 UTC-7', 'team1' => 'Switzerland',         'team2' => 'UEFA Path A winner'],
            ['round' => 'Matchday 8',  'date' => '2026-06-18', 'time' => '15:00 UTC-7', 'team1' => 'Canada',             'team2' => 'Qatar'],
            ['round' => 'Matchday 14', 'date' => '2026-06-24', 'time' => '12:00 UTC-7', 'team1' => 'Switzerland',         'team2' => 'Canada'],
            ['round' => 'Matchday 14', 'date' => '2026-06-24', 'time' => '12:00 UTC-7', 'team1' => 'UEFA Path A winner',  'team2' => 'Qatar'],
            // --- Groupe C ---
            ['round' => 'Matchday 3',  'date' => '2026-06-13', 'time' => '18:00 UTC-4', 'team1' => 'Brazil',             'team2' => 'Morocco'],
            ['round' => 'Matchday 3',  'date' => '2026-06-13', 'time' => '21:00 UTC-4', 'team1' => 'Haiti',              'team2' => 'Scotland'],
            ['round' => 'Matchday 9',  'date' => '2026-06-19', 'time' => '18:00 UTC-4', 'team1' => 'Scotland',           'team2' => 'Morocco'],
            ['round' => 'Matchday 9',  'date' => '2026-06-19', 'time' => '21:00 UTC-4', 'team1' => 'Brazil',             'team2' => 'Haiti'],
            ['round' => 'Matchday 14', 'date' => '2026-06-24', 'time' => '18:00 UTC-4', 'team1' => 'Scotland',           'team2' => 'Brazil'],
            ['round' => 'Matchday 14', 'date' => '2026-06-24', 'time' => '18:00 UTC-4', 'team1' => 'Morocco',            'team2' => 'Haiti'],
            // --- Groupe D ---
            ['round' => 'Matchday 2',  'date' => '2026-06-12', 'time' => '18:00 UTC-7', 'team1' => 'USA',                'team2' => 'Paraguay'],
            ['round' => 'Matchday 3',  'date' => '2026-06-13', 'time' => '21:00 UTC-7', 'team1' => 'Australia',          'team2' => 'UEFA Path C winner'],
            ['round' => 'Matchday 9',  'date' => '2026-06-19', 'time' => '12:00 UTC-7', 'team1' => 'USA',                'team2' => 'Australia'],
            ['round' => 'Matchday 9',  'date' => '2026-06-19', 'time' => '21:00 UTC-7', 'team1' => 'UEFA Path C winner',  'team2' => 'Paraguay'],
            ['round' => 'Matchday 15', 'date' => '2026-06-25', 'time' => '19:00 UTC-7', 'team1' => 'UEFA Path C winner',  'team2' => 'USA'],
            ['round' => 'Matchday 15', 'date' => '2026-06-25', 'time' => '19:00 UTC-7', 'team1' => 'Paraguay',           'team2' => 'Australia'],
            // --- Groupe E ---
            ['round' => 'Matchday 4',  'date' => '2026-06-14', 'time' => '12:00 UTC-5', 'team1' => 'Germany',            'team2' => 'Curaçao'],
            ['round' => 'Matchday 4',  'date' => '2026-06-14', 'time' => '19:00 UTC-4', 'team1' => 'Ivory Coast',        'team2' => 'Ecuador'],
            ['round' => 'Matchday 10', 'date' => '2026-06-20', 'time' => '16:00 UTC-4', 'team1' => 'Germany',            'team2' => 'Ivory Coast'],
            ['round' => 'Matchday 10', 'date' => '2026-06-20', 'time' => '19:00 UTC-5', 'team1' => 'Ecuador',            'team2' => 'Curaçao'],
            ['round' => 'Matchday 15', 'date' => '2026-06-25', 'time' => '16:00 UTC-4', 'team1' => 'Curaçao',           'team2' => 'Ivory Coast'],
            ['round' => 'Matchday 15', 'date' => '2026-06-25', 'time' => '16:00 UTC-4', 'team1' => 'Ecuador',            'team2' => 'Germany'],
            // --- Groupe F ---
            ['round' => 'Matchday 4',  'date' => '2026-06-14', 'time' => '15:00 UTC-5', 'team1' => 'Netherlands',        'team2' => 'Japan'],
            ['round' => 'Matchday 4',  'date' => '2026-06-14', 'time' => '20:00 UTC-6', 'team1' => 'UEFA Path B winner',  'team2' => 'Tunisia'],
            ['round' => 'Matchday 10', 'date' => '2026-06-20', 'time' => '12:00 UTC-5', 'team1' => 'Netherlands',        'team2' => 'UEFA Path B winner'],
            ['round' => 'Matchday 10', 'date' => '2026-06-20', 'time' => '22:00 UTC-6', 'team1' => 'Tunisia',            'team2' => 'Japan'],
            ['round' => 'Matchday 15', 'date' => '2026-06-25', 'time' => '18:00 UTC-5', 'team1' => 'Japan',              'team2' => 'UEFA Path B winner'],
            ['round' => 'Matchday 15', 'date' => '2026-06-25', 'time' => '18:00 UTC-5', 'team1' => 'Tunisia',            'team2' => 'Netherlands'],
            // --- Groupe G ---
            ['round' => 'Matchday 5',  'date' => '2026-06-15', 'time' => '12:00 UTC-7', 'team1' => 'Belgium',            'team2' => 'Egypt'],
            ['round' => 'Matchday 5',  'date' => '2026-06-15', 'time' => '18:00 UTC-7', 'team1' => 'Iran',               'team2' => 'New Zealand'],
            ['round' => 'Matchday 11', 'date' => '2026-06-21', 'time' => '12:00 UTC-7', 'team1' => 'Belgium',            'team2' => 'Iran'],
            ['round' => 'Matchday 11', 'date' => '2026-06-21', 'time' => '18:00 UTC-7', 'team1' => 'New Zealand',        'team2' => 'Egypt'],
            ['round' => 'Matchday 16', 'date' => '2026-06-26', 'time' => '20:00 UTC-7', 'team1' => 'Egypt',              'team2' => 'Iran'],
            ['round' => 'Matchday 16', 'date' => '2026-06-26', 'time' => '20:00 UTC-7', 'team1' => 'New Zealand',        'team2' => 'Belgium'],
            // --- Groupe H ---
            ['round' => 'Matchday 5',  'date' => '2026-06-15', 'time' => '12:00 UTC-4', 'team1' => 'Spain',              'team2' => 'Cape Verde'],
            ['round' => 'Matchday 5',  'date' => '2026-06-15', 'time' => '18:00 UTC-4', 'team1' => 'Saudi Arabia',       'team2' => 'Uruguay'],
            ['round' => 'Matchday 11', 'date' => '2026-06-21', 'time' => '12:00 UTC-4', 'team1' => 'Spain',              'team2' => 'Saudi Arabia'],
            ['round' => 'Matchday 11', 'date' => '2026-06-21', 'time' => '18:00 UTC-4', 'team1' => 'Uruguay',            'team2' => 'Cape Verde'],
            ['round' => 'Matchday 16', 'date' => '2026-06-26', 'time' => '19:00 UTC-5', 'team1' => 'Cape Verde',         'team2' => 'Saudi Arabia'],
            ['round' => 'Matchday 16', 'date' => '2026-06-26', 'time' => '18:00 UTC-6', 'team1' => 'Uruguay',            'team2' => 'Spain'],
            // --- Groupe I ---
            ['round' => 'Matchday 6',  'date' => '2026-06-16', 'time' => '15:00 UTC-4', 'team1' => 'France',             'team2' => 'Senegal'],
            ['round' => 'Matchday 6',  'date' => '2026-06-16', 'time' => '18:00 UTC-4', 'team1' => 'IC Path 2 winner',   'team2' => 'Norway'],
            ['round' => 'Matchday 12', 'date' => '2026-06-22', 'time' => '17:00 UTC-4', 'team1' => 'France',             'team2' => 'IC Path 2 winner'],
            ['round' => 'Matchday 12', 'date' => '2026-06-22', 'time' => '20:00 UTC-4', 'team1' => 'Norway',             'team2' => 'Senegal'],
            ['round' => 'Matchday 16', 'date' => '2026-06-26', 'time' => '15:00 UTC-4', 'team1' => 'Norway',             'team2' => 'France'],
            ['round' => 'Matchday 16', 'date' => '2026-06-26', 'time' => '15:00 UTC-4', 'team1' => 'Senegal',            'team2' => 'IC Path 2 winner'],
            // --- Groupe J ---
            ['round' => 'Matchday 6',  'date' => '2026-06-16', 'time' => '20:00 UTC-5', 'team1' => 'Argentina',          'team2' => 'Algeria'],
            ['round' => 'Matchday 6',  'date' => '2026-06-16', 'time' => '21:00 UTC-7', 'team1' => 'Austria',            'team2' => 'Jordan'],
            ['round' => 'Matchday 12', 'date' => '2026-06-22', 'time' => '12:00 UTC-5', 'team1' => 'Argentina',          'team2' => 'Austria'],
            ['round' => 'Matchday 12', 'date' => '2026-06-22', 'time' => '20:00 UTC-7', 'team1' => 'Jordan',             'team2' => 'Algeria'],
            ['round' => 'Matchday 17', 'date' => '2026-06-27', 'time' => '21:00 UTC-5', 'team1' => 'Algeria',            'team2' => 'Austria'],
            ['round' => 'Matchday 17', 'date' => '2026-06-27', 'time' => '21:00 UTC-5', 'team1' => 'Jordan',             'team2' => 'Argentina'],
            // --- Groupe K ---
            ['round' => 'Matchday 7',  'date' => '2026-06-17', 'time' => '12:00 UTC-5', 'team1' => 'Portugal',           'team2' => 'IC Path 1 winner'],
            ['round' => 'Matchday 7',  'date' => '2026-06-17', 'time' => '20:00 UTC-6', 'team1' => 'Uzbekistan',         'team2' => 'Colombia'],
            ['round' => 'Matchday 13', 'date' => '2026-06-23', 'time' => '12:00 UTC-5', 'team1' => 'Portugal',           'team2' => 'Uzbekistan'],
            ['round' => 'Matchday 13', 'date' => '2026-06-23', 'time' => '20:00 UTC-6', 'team1' => 'Colombia',           'team2' => 'IC Path 1 winner'],
            ['round' => 'Matchday 17', 'date' => '2026-06-27', 'time' => '19:30 UTC-4', 'team1' => 'Colombia',           'team2' => 'Portugal'],
            ['round' => 'Matchday 17', 'date' => '2026-06-27', 'time' => '19:30 UTC-4', 'team1' => 'IC Path 1 winner',   'team2' => 'Uzbekistan'],
            // --- Groupe L ---
            ['round' => 'Matchday 7',  'date' => '2026-06-17', 'time' => '15:00 UTC-5', 'team1' => 'England',            'team2' => 'Croatia'],
            ['round' => 'Matchday 7',  'date' => '2026-06-17', 'time' => '19:00 UTC-4', 'team1' => 'Ghana',              'team2' => 'Panama'],
            ['round' => 'Matchday 13', 'date' => '2026-06-23', 'time' => '16:00 UTC-4', 'team1' => 'England',            'team2' => 'Ghana'],
            ['round' => 'Matchday 13', 'date' => '2026-06-23', 'time' => '19:00 UTC-4', 'team1' => 'Panama',             'team2' => 'Croatia'],
            ['round' => 'Matchday 17', 'date' => '2026-06-27', 'time' => '17:00 UTC-4', 'team1' => 'Panama',             'team2' => 'England'],
            ['round' => 'Matchday 17', 'date' => '2026-06-27', 'time' => '17:00 UTC-4', 'team1' => 'Croatia',            'team2' => 'Ghana'],
        ];

        foreach ($groupMatches as $match) {
            $homeCode = $teamNameToCode[$match['team1']] ?? null;
            $awayCode = $teamNameToCode[$match['team2']] ?? null;

            if (! $homeCode || ! $awayCode) {
                // Équipe non reconnue dans le mapping – on passe
                continue;
            }

            $homeTeam = $teamIndex->get($homeCode);
            $awayTeam = $teamIndex->get($awayCode);

            if (! $homeTeam || ! $awayTeam) {
                continue;
            }

            // Conversion en heure de Paris (CEST = UTC+2, en vigueur tout juin/juillet)
            // Format JSON : "HH:MM UTC±N" (ex. "13:00 UTC-6")
            preg_match('/(\d{2}):(\d{2})\s+UTC([+-]\d+(?:\.\d+)?)/', $match['time'], $parts);
            $h        = (int) ($parts[1] ?? 0);
            $m        = (int) ($parts[2] ?? 0);
            $offset   = isset($parts[3]) ? (float) $parts[3] : 0;
            // Ramène en UTC puis ajoute +2h pour Paris (CEST)
            $playedAt = \Carbon\Carbon::parse($match['date'])
                ->setTime($h, $m)
                ->subHours($offset)   // -> UTC
                ->addHours(2);        // -> Europe/Paris (CEST, UTC+2)

            Fixture::create([
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'phase'        => 'group',
                'played_at'    => $playedAt,
            ]);
        }
    }
}
