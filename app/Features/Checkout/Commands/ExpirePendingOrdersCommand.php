<?php

declare(strict_types=1);

namespace App\Features\Checkout\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;

class ExpirePendingOrdersCommand extends Command
{
    protected $signature = 'app:expire-pending-orders';

    protected $description = 'Set status=Failed on pending orders older than 1 hour';

    public function handle(): int
    {
        $updated = Order::query()
            ->where('status', OrderStatus::Pending)
            ->where('created_at', '<', now()->subHour())
            ->update(['status' => OrderStatus::Failed]);

        $this->info("Expired {$updated} pending order(s).");

        return self::SUCCESS;
    }
}
