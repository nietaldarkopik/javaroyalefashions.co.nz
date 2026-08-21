<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE hero_slides MODIFY heading VARCHAR(191) NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE hero_slides SET heading = '' WHERE heading IS NULL");
        DB::statement('ALTER TABLE hero_slides MODIFY heading VARCHAR(191) NOT NULL');
    }
};
