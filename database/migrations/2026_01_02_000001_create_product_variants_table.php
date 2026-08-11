<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku', 60)->nullable()->unique();

            // Fixed common attributes (fast to filter/display on), plus one
            // free-form pair for anything else — a full attribute/EAV system
            // would be overkill for a catalog site with no variant reporting
            // requirements; this covers "size, color, and one more" without it.
            $table->string('size', 60)->nullable();
            $table->string('color', 60)->nullable();
            $table->string('attribute_name', 60)->nullable();
            $table->string('attribute_value', 120)->nullable();

            // Null price/image means "inherit from the parent product".
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
