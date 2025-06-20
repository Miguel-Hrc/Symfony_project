<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\UserRepository;
use App\Security\EmailVerifierAuthenticator;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('anonymikuw@outlook.fr'))
                ->to($user->getEmail())
                ->subject('Confirmez votre email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

            return $this->redirectToRoute('app_registration_request');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/check-email', name: 'app_registration_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);
        return $this->render('registration/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
        
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator,
        UserRepository $userRepository
    ): Response {
        $id = $request->query->get('id');
        if (!$id) {
            $this->addFlash('verify_email_error', 'Lien invalide ou manquant.');
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);
        if (!$user) {
            $this->addFlash('verify_email_error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre email a été vérifié.');

        $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );

        return $this->redirectToRoute('app_home');
    }
}
