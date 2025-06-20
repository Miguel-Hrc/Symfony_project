<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sweatshirt;

class StripeService
{
    private string $stripeSecretKey;
    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        string $stripeSecretKey,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
    ) {
        $this->stripeSecretKey = $stripeSecretKey;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * Crée une session Stripe Checkout pour un panier donné
     *
     * @param array $cart Tableau d'items : [ ['product_id'=>int, 'size_id'=>string, 'quantity'=>int], ... ]
     * @return string URL de redirection vers Stripe Checkout
     * @throws \Exception si produit non trouvé
     */
    public function createCheckoutSession(array $cart): string
    {
        Stripe::setApiKey($this->stripeSecretKey);

        $lineItems = [];

        foreach ($cart as $item) {
            /** @var Sweatshirt|null $product */
            $product = $this->entityManager->getRepository(Sweatshirt::class)->find($item['product_id']);
            if (!$product) {
                throw new \Exception("Produit introuvable: ID " . $item['product_id']);
            }

            $size = $item['size_id'] ?? 'Taille inconnue';

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getName() . ' - Taille ' . $size,
                    ],
                    'unit_amount' => (int) ($product->getPrice()), 
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $session->url;
    }
}