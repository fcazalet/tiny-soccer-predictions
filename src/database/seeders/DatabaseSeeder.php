<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Team;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
// Admin
        User::create([
            'name'  => 'Admin',
            'email' => 'admin@worldcup.local',
            'role'  => 'admin',
        ]);

        // Groupes et équipes Coupe du Monde 2026
        $groups = [
            'A' => [['France', '🇫🇷'], ['Maroc', '🇲🇦'], ['Argentine', '🇦🇷'], ['Canada', '🇨🇦']],
            'B' => [['Espagne', '🇪🇸'], ['Brésil', '🇧🇷'], ['Japon', '🇯🇵'], ['Mexique', '🇲🇽']],
            'C' => [['Angleterre', '🏴󠁧󠁢󠁥󠁮󠁧󠁿'], ['Allemagne', '🇩🇪'], ['Portugal', '🇵🇹'], ['USA', '🇺🇸']],
            'D' => [['Pays-Bas', '🇳🇱'], ['Belgique', '🇧🇪'], ['Sénégal', '🇸🇳'], ['Australie', '🇦🇺']],
            'E' => [['Italie', '🇮🇹'], ['Croatie', '🇭🇷'], ['Uruguay', '🇺🇾'], ['Équateur', '🇪🇨']],
            'F' => [['Colombie', '🇨🇴'], ['Danemark', '🇩🇰'], ['Nigeria', '🇳🇬'], ['Corée du Sud', '🇰🇷']],
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
    }
}
