<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SweatshirtRepository;
use App\Entity\Sweatshirt;
use App\Entity\ProductSize;
use App\Form\ProductFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Entity\Size;


class AdminController extends AbstractController

{   
#[Route('/admin', name: 'app_admin', methods: ['GET', 'POST'])]

public function index(Request $request, ManagerRegistry $doctrine, SweatshirtRepository $sweatshirtRepository): Response
    {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $em = $doctrine->getManager();

    if ($request->isMethod('POST') && $request->request->has('delete_id')) {
        $deleteId = $request->request->get('delete_id');
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $deleteId, $submittedToken)) {
            $sweatshirtToDelete = $sweatshirtRepository->find($deleteId);
            if ($sweatshirtToDelete) {
                $em->remove($sweatshirtToDelete);
                $em->flush();
                $this->addFlash('success', 'Produit supprimé !');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin');
    }

    $sweatshirts = $sweatshirtRepository->findAll();
    $editForms = [];

    if ($request->isMethod('POST') && $request->request->has('edit_id')) {
        $editId = (int) $request->request->get('edit_id');
        $sweatshirtToEdit = $sweatshirtRepository->find($editId);

        if ($sweatshirtToEdit) {
            $form = $this->createForm(ProductFormType::class, $sweatshirtToEdit, [
                'action' => $this->generateUrl('app_admin'),
                'method' => 'POST',
                'em' => $em,
            ]);
            
            $form->handleRequest($request);
            dump($request->files);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->handleImageUpload($form, $sweatshirtToEdit);
                $em->flush();
                $this->addFlash('success', 'Produit modifié !');
                return $this->redirectToRoute('app_admin');
            }

            $editForms[$editId] = $form->createView();
        }
    }

    foreach ($sweatshirts as $sw) {
        if (!isset($editForms[$sw->getId()])) {
            $form = $this->createForm(ProductFormType::class, $sw, [
                'action' => $this->generateUrl('app_admin'),
                'method' => 'POST',
                'em' => $em,
            ]);
            $editForms[$sw->getId()] = $form->createView();
        }
    }
    
    $newSweatshirt = new Sweatshirt();
    $sizes = $doctrine->getRepository(Size::class)->findAll();
    foreach ($sizes as $size) {
        $ps = new ProductSize();
        $ps->setSize($size);
        $ps->setProduct($newSweatshirt);
        $newSweatshirt->addProductSize($ps);
    }

    $addForm = $this->createForm(ProductFormType::class, $newSweatshirt,[
    'em' => $em,]);
    $addForm->handleRequest($request);
    dump($request->files);

    if ($request->isMethod('POST') && $request->request->has('add_mode')) {
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $this->handleImageUpload($addForm, $newSweatshirt);
            $em->persist($newSweatshirt);
            $em->flush();
            $this->addFlash('success', 'Produit ajouté !');
            return $this->redirectToRoute('app_admin');
        }
    }

    return $this->render('admin/index.html.twig', [
        'sweatshirts' => $sweatshirts,
        'add_form' => $addForm->createView(),
        'edit_forms' => $editForms,
        'sizes' => $sizes,
        ]);
    }

    private function handleImageUpload($form, Sweatshirt $sweatshirt): void
    {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            $oldFilename = $sweatshirt->getImageName();

            if ($oldFilename && $oldFilename !== 'default.png') {
                $oldFilePath = $this->getParameter('uploads_directory') . '/' . $oldFilename;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );

            $sweatshirt->setImageName($newFilename);
        }
    }
}