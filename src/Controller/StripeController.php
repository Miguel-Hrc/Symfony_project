<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Request;
use App\Service\StripeService;

class StripeController extends AbstractController
{
    #[Route('/checkout', name: 'stripe_checkout')]
    public function checkout(Request $request, StripeService $stripeService): Response
{
    $cart = $request->getSession()->get('cart', []);

    if (empty($cart)) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('app_cart');
    }

    $checkoutUrl = $stripeService->createCheckoutSession($cart);

    return $this->redirect($checkoutUrl);
}

    #[Route('/checkout/success', name: 'stripe_success')]
    public function success(): Response
    {
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/checkout/cancel', name: 'stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}