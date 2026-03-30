<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Events\OrderPaid;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\UserBook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessPaymentConfirmation implements ShouldQueue
{
    use Queueable;

    /** Maximum number of attempts before the job is marked as failed. */
    public int $tries = 3;

    /** Seconds to wait before retrying after a failure. */
    public int $backoff = 10;

    public function __construct(
        public readonly int $orderId,
        public readonly string $stripePaymentIntentId,
        public readonly string $stripeSessionId,
    ) {}

    public function handle(): void
    {
        $order = Order::query()->find($this->orderId);

        if ($order === null) {
            return;
        }

        // Rule 30: idempotency — if already paid, skip
        if ($order->status === OrderStatus::Paid) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $order->status = OrderStatus::Paid;
            $order->paid_at = now();
            $order->stripe_payment_intent_id = $this->stripePaymentIntentId;
            $order->save();

            // Rule 31: create user_books records for each order item
            $order->load('items');

            foreach ($order->items as $item) {
                UserBook::query()->firstOrCreate(
                    ['user_id' => $order->user_id, 'book_id' => $item->book_id],
                    ['order_id' => $order->id, 'granted_at' => now()],
                );
            }

            // Rule 31: clear the user's cart by user_id (no session in queue context)
            CartItem::query()
                ->where('user_id', $order->user_id)
                ->delete();
        });

        // Rule 31/32: dispatch OrderPaid event to trigger confirmation email
        OrderPaid::dispatch($order->fresh());
    }
}
