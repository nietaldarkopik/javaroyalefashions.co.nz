<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Admin/staff role. Every row in this table is a staff login —
            // customers never get one (guest checkout only). Kept simple
            // (single string, no roles table) since only "admin" is used
            // today; a permissions table is a future-upgrade concern.
            $table->string('role', 20)->default('admin')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
