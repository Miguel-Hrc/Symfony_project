<?php

namespace App\EventListener;

use App\Service\StripeService;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class StripeBootListener
{
    private StripeService $stripeService;
    private bool $hasRun = false;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->hasRun) return;
        if ($_ENV['APP_ENV'] !== 'dev') return;

        $this->hasRun = true;

        $cart = [
            ['product_id' => 1, 'size_id' => 'M', 'quantity' => 1],
        ];

        try {
            $url = $this->stripeService->createCheckoutSession($cart);
            dump(" Stripe session created: " . $url);
        } catch (\Throwable $e) {
            dump(" Stripe Error: " . $e->getMessage());
        }
    }
}