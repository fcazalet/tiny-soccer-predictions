<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the Admin part.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate([
            'name'  => 'Admin',
            'email' => 'admin@tinysp.local',
            'role'  => 'admin',
        ]);
    }
}
