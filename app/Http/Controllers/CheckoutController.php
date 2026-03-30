<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\PaymentProvider;
use App\Enums\OrderStatus;
use App\Exceptions\EmptyCartException;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentProvider $paymentProvider,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        try {
            $order = $this->orderService->createFromCart($user, session()->getId());
        } catch (EmptyCartException) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Корзина пуста.']);
        }

        try {
            $session = $this->paymentProvider->createSession($order, $user);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error during checkout session creation', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            $order->status = OrderStatus::Failed;
            $order->save();

            return redirect()->route('cart.index')
                ->withErrors(['cart' => 'Ошибка при создании платежа. Попробуйте позже.']);
        }

        $order->stripe_session_id = $session['id'];
        $order->save();

        return redirect()->away($session['url']);
    }

    public function success(Request $request): View
    {
        return view('checkout.success');
    }
}
