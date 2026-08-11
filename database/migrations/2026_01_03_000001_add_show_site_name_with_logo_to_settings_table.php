<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Storefront-only toggle: when a logo image is set, should the
            // site name text still show alongside it? Some logos already
            // contain the brand name graphically (wordmark) and don't need
            // it repeated; others are icon-only and do. Admin panel
            // branding is unaffected by this — it always shows both, since
            // that's a separate, internal-tool context.
            $table->boolean('show_site_name_with_logo')->default(true)->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('show_site_name_with_logo');
        });
    }
};
