<?php

declare(strict_types=1);

namespace App\Features\Checkout\Controllers;

use App\Features\Checkout\Services\StripePaymentProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class WebhookController extends Controller
{
    public function __construct(private readonly StripePaymentProvider $stripeProvider) {}

    /**
     * Handle Stripe webhook events.
     *
     * Rule 35: signature verification is performed inside StripePaymentProvider.
     * Rule 29: webhook is the source of truth for payment confirmation.
     */
    public function handleStripe(Request $request): Response
    {
        try {
            $this->stripeProvider->handleWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
            );
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);

            return response('Signature verification failed', 400);
        } catch (UnexpectedValueException $e) {
            Log::warning('Stripe webhook invalid payload', ['error' => $e->getMessage()]);

            return response('Invalid payload', 400);
        }

        return response('OK', 200);
    }
}
