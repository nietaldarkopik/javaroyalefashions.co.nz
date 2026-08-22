<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_banners', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow', 60)->nullable();
            $table->string('heading', 191)->nullable();
            $table->text('body')->nullable();
            $table->string('button_text', 60)->nullable();
            $table->string('button_url', 255)->nullable();
            $table->string('image_path')->nullable();
            // Fixed set of front-end pages this banner can appear on — see
            // ContentBanner::PAGES. Stored as JSON rather than a pivot table
            // since the page list is a small, code-defined enum, not a
            // model of its own.
            $table->json('pages')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_banners');
    }
};
