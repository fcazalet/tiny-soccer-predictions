<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        DB::statement("ALTER TABLE fixtures MODIFY phase ENUM('group','r32','r16','qf','sf','final')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE fixtures MODIFY phase ENUM('group','r16','qf','sf','final')");
    }
};
