<?php

namespace UserBundle\Controller;

use AppBundle\Entity\User;
use DoctrineExtensions\Query\Mysql\Date;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Form\Type\UserType;

/**
 * @Route("/register")
 */
class RegistrationController extends Controller {
    /**
     * @Route("/", name="register")
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request) {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var $dispatcher EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');
            $userManager = $this->get('fos_user.user_manager');
            $user = new User();
            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() and $form->isValid()) {
                $user->setRegistrationDate(new \DateTime('now'));

                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                $this->addFlash('success', 'Votre inscription a bien été prise en compte. Vous allez recevoir un mail de confirmation contenant un lien d\'activation.');

                return $this->redirectToRoute('home');
            }

            return $this->render('UserBundle:Registration:register.html.twig', [
                'form' => $form->createView()
            ]);
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     * @param Request $request
     * @param string  $token
     * @return Response
     */
    public function confirmAction(Request $request, $token) {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserByConfirmationToken($token);

            if (null === $user) {
                //throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
                $this->addFlash('danger', 'Ce lien semble invalide. Veuillez vérifier que vous avez entrez une adresse correcte.');
                return $this->redirectToRoute('home');
            }

            $this->addFlash('success', 'Votre compte a été activé avec succès.');

            /** @var $dispatcher EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            $user->setConfirmationToken(null);
            $user->setEnabled(true);

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('home');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->redirectToRoute('home');
    }
}