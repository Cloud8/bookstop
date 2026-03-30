<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeGuestCartOnLogin
{
    public function __construct(private readonly CartService $cartService) {}

    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        $sessionId = session()->getId();

        $this->cartService->mergeGuestCart($user, $sessionId);
    }
}
