<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::Pending,
            'total_amount' => fake()->numberBetween(29900, 199900),
            'currency' => 'RUB',
            'payment_provider' => 'stripe',
            'stripe_session_id' => null,
            'stripe_payment_intent_id' => null,
            'paid_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Pending,
            'paid_at' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Paid,
            'stripe_session_id' => 'cs_test_'.fake()->regexify('[a-zA-Z0-9]{40}'),
            'stripe_payment_intent_id' => 'pi_test_'.fake()->regexify('[a-zA-Z0-9]{24}'),
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Failed,
            'paid_at' => null,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Refunded,
            'paid_at' => now()->subDay(),
        ]);
    }
}
