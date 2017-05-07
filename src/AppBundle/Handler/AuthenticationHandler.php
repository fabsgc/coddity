<?php

namespace AppBundle\Handler;

use AppBundle\Entity\LoginRecord;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationHandler extends DefaultAuthenticationSuccessHandler
{
    use ContainerAwareTrait;


    /**
     * AuthenticationHandler constructor.
     */
    public function __construct( HttpUtils $httpUtils, array $options ) {
        parent::__construct( $httpUtils, $options );
    }


    function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $record = new LoginRecord();
        $record->setUser($user);
        $manager =  $this->container->get('doctrine')->getEntityManager();
        $manager->persist($record);
        $manager->flush();

        return parent::onAuthenticationSuccess($request, $token );

    }
}