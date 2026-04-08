<?php

declare(strict_types=1);

namespace App\Features\Cart\Commands;

use App\Models\CartItem;
use Illuminate\Console\Command;

class CleanupCartsCommand extends Command
{
    protected $signature = 'app:cleanup-carts';

    protected $description = 'Delete cart items older than 7 days';

    public function handle(): int
    {
        $deleted = CartItem::query()
            ->where('created_at', '<', now()->subDays(7))
            ->delete();

        $this->info("Deleted {$deleted} stale cart item(s).");

        return self::SUCCESS;
    }
}
