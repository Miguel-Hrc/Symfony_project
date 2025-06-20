<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Sweatshirt;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\CartRemoveType;


use App\Repository\ProductSizeRepository;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
public function index(SessionInterface $session, ProductSizeRepository $psRepo): Response
{
    $cart = $session->get('cart', []);
    $cartDetails = [];
    $total = 0;
    $forms = [];

    foreach ($cart as $key => $item) {
        $productSize = $psRepo->findOneBy([
            'product' => $item['product_id'],
            'size' => $item['size_id'],
        ]);

        if (!$productSize) continue;

        $product = $productSize->getProduct();
        $size = $productSize->getSize();
        $quantity = $item['quantity'];
        $total += $product->getPrice() * $quantity;

        $cartDetails[] = [
            'product' => $product,
            'size' => $size,
            'quantity' => $quantity,
        ];
        $forms[$key] = $this->createForm(CartRemoveType::class, null, [
            'action' => $this->generateUrl('cart_remove', [
                'productId' => $item['product_id'],
                'sizeId' => $item['size_id'],
            ]),
            'method' => 'DELETE',
        ])->createView();
    }

    return $this->render('cart/index.html.twig', [
        'cartDetails' => $cartDetails,
        'total' => $total,
        'forms' => $forms,
    ]);}

    #[Route('/cart/add', name: 'cart_add', methods: [ 'POST'])]
    public function addToCart(Request $request, SessionInterface $session, ProductSizeRepository $psRepo): Response
    {
        $productId = $request->request->get('product_id');
        $sizeId = $request->request->get('size_id');
        $quantity = (int) $request->request->get('quantity');

        if (!$productId || !$sizeId || $quantity < 1) {
            $this->addFlash('error', 'Données invalides.');
            return $this->redirectToRoute('app_list_product');
        }

        $productSize = $psRepo->findOneBy([
            'product' => $productId,
            'size' => $sizeId
        ]);

        if (!$productSize) {
            $this->addFlash('error', 'Cette taille n\'existe pas pour ce produit.');
            return $this->redirectToRoute('app_list_product');
        }

        $availableStock = $productSize->getQuantity();
        $cart = $session->get('cart', []);

        $existingQuantity = 0;

        foreach ($cart as &$item) {
            if ($item['product_id'] == $productId && $item['size_id'] == $sizeId) {
                $existingQuantity = $item['quantity'];

                if (($existingQuantity + $quantity) > $availableStock) {
                    $this->addFlash('error', 'Vous avez déjà ajouté ' . $existingQuantity . ' exemplaire(s), stock max : ' . $availableStock);
                    return $this->redirectToRoute('app_cart');
                }

                $item['quantity'] += $quantity;
                $session->set('cart', $cart);
                $this->addFlash('success', 'Produit ajouté au panier.');
                return $this->redirectToRoute('app_cart');
            }
        }

        if ($quantity > $availableStock) {
            $this->addFlash('error', 'Stock insuffisant pour cette taille.');
            return $this->redirectToRoute('app_cart');
        }

        $cart[] = [
            'product_id' => $productId,
            'size_id' => $sizeId,
            'quantity' => $quantity,
        ];

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('app_cart');
    }
    
 

    #[Route('/cart/remove/{productId}/{sizeId}', name: 'cart_remove', methods: ['DELETE'])]
public function remove(int $productId, int $sizeId, Request $request, SessionInterface $session): Response
{
    $form = $this->createForm(CartRemoveType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $cart = $session->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $productId && $item['size_id'] == $sizeId) {
                unset($cart[$key]);
                break;
            }
        }

        // Réindexer le tableau pour éviter les trous d'index
        $cart = array_values($cart);
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    throw $this->createAccessDeniedException('Invalid form submission');
}

    #[Route('/cart/increase/{productId}/{sizeId}', name: 'cart_increase')]
    public function increaseQuantity(int $productId, int $sizeId, SessionInterface $session, ProductSizeRepository $psRepo): Response
    {
        $cart = $session->get('cart', []);
        $productSize = $psRepo->findOneBy([
            'product' => $productId,
            'size' => $sizeId,
        ]);

        if (!$productSize) {
            $this->addFlash('error', 'Taille invalide.');
            return $this->redirectToRoute('app_cart');
        }

        foreach ($cart as &$item) {
            if ($item['product_id'] == $productId && $item['size_id'] == $sizeId) {
                if ($item['quantity'] < $productSize->getQuantity()) {
                    $item['quantity']++;
                } else {
                    $this->addFlash('error', 'Stock maximum atteint.');
                }
                break;
            }
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/cart/decrease/{productId}/{sizeId}', name: 'cart_decrease')]
    public function decreaseQuantity(int $productId, int $sizeId, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        foreach ($cart as $key => &$item) {
            if ($item['product_id'] == $productId && $item['size_id'] == $sizeId) {
                $item['quantity']--;
                if ($item['quantity'] <= 0) {
                    unset($cart[$key]);
                }
                break;
            }
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
}