<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-row site configuration. A fixed, small, known field set —
        // fixed columns give type safety that a key-value table would only
        // fake with runtime lookups. Enforced as a singleton (id = 1) in
        // App\Services\SettingService, not at the schema level.
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 120);
            $table->string('site_tagline', 200)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();

            $table->string('contact_email', 150);
            $table->string('contact_phone', 30)->nullable();
            $table->string('contact_whatsapp', 30)->nullable();
            $table->string('address', 255)->nullable();

            $table->string('bank_name', 120);
            $table->string('bank_account_name', 150);
            $table->string('bank_account_number', 60);
            $table->string('bank_swift_code', 20)->nullable();

            $table->decimal('shipping_urban_rate', 10, 2)->default(13.00);
            $table->decimal('shipping_rural_rate', 10, 2)->default(18.00);
            $table->string('currency_code', 3)->default('NZD');

            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
