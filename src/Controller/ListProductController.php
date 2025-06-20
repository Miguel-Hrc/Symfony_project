<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SweatshirtRepository;

class ListProductController extends AbstractController
{
    #[Route('/product', name: 'app_list_product', methods: ['GET'])]
    public function index(Request $request, SweatshirtRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $priceRanges = [
            '10-29' => ['min' => 1000, 'max' => 2900],
            '29-35' => ['min' => 2900, 'max' => 3500],
            '35-50' => ['min' => 3500, 'max' => 5000],
        ];

        $selectedKey = $request->query->get('range', '');
        $minPrice = null;
        $maxPrice = null;

        if ($selectedKey !== '' && isset($priceRanges[$selectedKey])) {
            $minPrice = $priceRanges[$selectedKey]['min'];
            $maxPrice = $priceRanges[$selectedKey]['max'];
        }

        if ($minPrice !== null && $maxPrice !== null) {
            $sweatshirts = $repository->findByPriceRange($minPrice, $maxPrice);
        } else {
            $sweatshirts = $repository->findAll();
        }

        $labels = [
            '10-29' => '10 à 29 €',
            '29-35' => '29 à 35 €',
            '35-50' => '35 à 50 €',
        ];

        return $this->render('list_product/index.html.twig', [
            'sweatshirts'   => $sweatshirts,
            'priceRanges'   => $priceRanges,
            'rangeLabels'   => $labels,
            'selectedRange' => $selectedKey,
        ]);
    }
}