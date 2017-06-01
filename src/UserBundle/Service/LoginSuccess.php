<?php

namespace UserBundle\Service;

use Symfony\Component\Routing\RouterInterface,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginSuccess implements AuthenticationSuccessHandlerInterface
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($token->getUser()->hasRole('ROLE_ADMIN')) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        }
        else {
            return new RedirectResponse($this->router->generate('home'));
        }
    }
}