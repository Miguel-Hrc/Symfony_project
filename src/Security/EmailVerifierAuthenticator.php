<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class EmailVerifierAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): bool
    {
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        throw new \LogicException('This authenticator should not be used via authenticate().');
    }

    public function createPassport(UserInterface $user): Passport
    {
        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier(), fn () => $user));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?\Symfony\Component\HttpFoundation\Response
    {
        return null; 
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        return null;
    }
}