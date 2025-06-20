<?php


namespace App\Command;

use App\Service\StripeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test-stripe-checkout',
    description: 'Teste le service Stripe en simulant un panier',
)]
class TestStripeCheckoutCommand extends Command
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        parent::__construct();
        $this->stripeService = $stripeService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("➡️  Test du service Stripe...");

        $cart = [
            [
                'product_id' => 1,
                'size_id' => 'M',
                'quantity' => 1,
            ],
        ];

        try {
            $checkoutUrl = $this->stripeService->createCheckoutSession($cart);
            $output->writeln("✅ Session Stripe créée avec succès !");
            $output->writeln("🔗 URL de paiement : $checkoutUrl");
        } catch (\Throwable $e) {
            $output->writeln("<error>❌ Erreur : {$e->getMessage()}</error>");
        }

        return Command::SUCCESS;
    }
}