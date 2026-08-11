<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();

            // Snapshots — deliberately duplicated off `customers` so an
            // order's paper trail never changes if the customer record is
            // edited later.
            $table->string('customer_name', 150);
            $table->string('customer_email', 150);
            $table->string('customer_phone', 30);

            $table->string('shipping_address_line1', 200);
            $table->string('shipping_address_line2', 200)->nullable();
            $table->string('shipping_suburb', 120)->nullable();
            $table->string('shipping_city', 120);
            $table->string('shipping_region', 120)->nullable();
            $table->string('shipping_postcode', 10);
            $table->string('shipping_area', 10);

            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('grand_total', 10, 2);
            $table->string('currency', 3)->default('NZD');

            $table->string('status', 30)->default('pending_payment');
            $table->string('payment_method', 30)->default('bank_transfer');

            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('invoice_sent_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
