<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MODIFY is MySQL/MariaDB-only; other drivers (the SQLite test
        // database) go through the schema builder instead.
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('hero_slides', fn (Blueprint $table) => $table->string('heading', 191)->nullable()->change());

            return;
        }

        DB::statement('ALTER TABLE hero_slides MODIFY heading VARCHAR(191) NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE hero_slides SET heading = '' WHERE heading IS NULL");

        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('hero_slides', fn (Blueprint $table) => $table->string('heading', 191)->nullable(false)->change());

            return;
        }

        DB::statement('ALTER TABLE hero_slides MODIFY heading VARCHAR(191) NOT NULL');
    }
};
