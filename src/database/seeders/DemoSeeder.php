<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
    }
}
