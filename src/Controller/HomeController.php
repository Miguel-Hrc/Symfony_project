<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sweatshirts = $entityManager->getRepository(Sweatshirt::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sweatshirts' => $sweatshirts,
        ]);
    }
}
