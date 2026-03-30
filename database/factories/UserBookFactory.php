<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBook>
 */
class UserBookFactory extends Factory
{
    protected $model = UserBook::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'order_id' => Order::factory(),
            'granted_at' => now(),
        ];
    }
}
