<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_invitations', function (Blueprint $table) {
            $table->id();

            // One invitation per order — it covers every product in it.
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();

            // Snapshot of where the link was sent, in case the order's
            // email is corrected later.
            $table->string('customer_email', 150);

            // SHA-256 of the emailed token. The plain token only ever
            // exists in the email itself, so a leaked database can't be
            // turned into working review links.
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at');

            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('send_count')->default(0);
            $table->timestamp('last_failed_at')->nullable();
            $table->string('last_error', 500)->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_invitations');
    }
};
