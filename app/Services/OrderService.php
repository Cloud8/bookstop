<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\EmptyCartException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private readonly CartService $cartService) {}

    /**
     * Create an Order from the user's current cart.
     *
     * Rule 27: Order is created BEFORE Stripe redirect.
     * Rule 28: order_items.price is a snapshot of the book price at purchase time.
     *
     * @throws EmptyCartException if the cart is empty
     */
    public function createFromCart(User $user, string $sessionId): Order
    {
        $items = $this->cartService->getItems($user, $sessionId);

        if ($items->isEmpty()) {
            throw new EmptyCartException('Корзина пуста.');
        }

        return DB::transaction(function () use ($user, $items, $sessionId): Order {
            $order = Order::query()->create([
                'user_id' => $user->id,
                'status' => OrderStatus::Pending,
                'total_amount' => $this->cartService->getTotalFromItems($items),
                'currency' => 'RUB',
                'payment_provider' => 'stripe',
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'book_id' => $item->book_id,
                    'price' => $item->book->price,
                    'currency' => 'RUB',
                ]);
            }

            $this->cartService->clearCart($user, $sessionId);

            $order->load('items.book');

            return $order;
        });
    }
}
