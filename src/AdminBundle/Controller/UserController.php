<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Util\Now;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AdminBundle\Form\Type\UserType;

/**
 * @Route("/user")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller
{

    /**
     * @Route("/list", name="admin_user_list")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render('AdminBundle:User:index.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Route("/edit/{user}", name="admin_user_edit")
     * @ParamConverter("user", class="AppBundle:User")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())  {
            /**
             * @var $userManager UserManager
             */
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', 'Modifications enregistrées');
        }

        return $this->render('AdminBundle:User:edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/register", name="admin_user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function registerUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setRegistrationDate(Now::now());

            /**
             * @var $userManager UserManager
             */
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', 'Inscription terminée');

            return $this->redirectToRoute('admin_user', array('user' => $user->getId()));
        }

        return $this->render('AdminBundle:User:register.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}
