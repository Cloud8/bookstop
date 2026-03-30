<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartItem>
 */
class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'session_id' => null,
            'book_id' => Book::factory(),
        ];
    }

    public function forGuest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'session_id' => fake()->uuid(),
        ]);
    }
}
