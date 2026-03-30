<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('total_amount')->default(0);
            $table->char('currency', 3)->default('RUB');
            $table->string('payment_provider', 30)->default('stripe');
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'orders_user_id_index');
            $table->index('status', 'orders_status_index');
            $table->unique('stripe_session_id', 'orders_stripe_session_id_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
