<?php

declare(strict_types=1);

namespace App\Features\Checkout\Contracts;

use Stripe\Exception\SignatureVerificationException;

interface SupportsWebhooks
{
    /**
     * Handle an incoming webhook from the payment provider.
     * Returns void — HTTP response is the controller's responsibility.
     * Throws SignatureVerificationException or UnexpectedValueException on bad payload.
     * Any other Throwable propagates up → Laravel returns 500 → provider retries.
     *
     * @throws SignatureVerificationException
     * @throws \UnexpectedValueException
     */
    public function handleWebhook(string $payload, string $signature): void;
}
