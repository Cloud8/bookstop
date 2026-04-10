<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('provider', 30);
            $table->json('provider_data');
            $table->string('status', 20)->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
        });

        // Remove Stripe-specific columns from orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_stripe_session_id_unique');
            $table->dropColumn(['stripe_session_id', 'stripe_payment_intent_id', 'payment_provider']);
        });
    }

    public function down(): void
    {
        // Restore Stripe-specific columns on orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_provider', 30)->default('stripe')->after('currency');
            $table->string('stripe_session_id')->nullable()->after('payment_provider');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_session_id');
            $table->unique('stripe_session_id', 'orders_stripe_session_id_unique');
        });

        Schema::dropIfExists('order_transactions');
    }
};
