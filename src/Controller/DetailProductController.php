<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailProductController extends AbstractController
{
    #[Route('/detail/product/{id}', name: 'app_detail_product', methods: ['GET'])]
    public function index(Sweatshirt $sweatshirt): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('detail_product/index.html.twig', [
            'sweatshirt' => $sweatshirt,
        ]);
    }
}
