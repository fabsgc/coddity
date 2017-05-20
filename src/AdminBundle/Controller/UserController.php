<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Util\Now;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        /**
         * @var $userRepo UserRepository
         */
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
            if($form->get("admin")->getData()) {
                $user->addRole('ROLE_ADMIN');
            }
            else {
                $user->removeRole('ROLE_ADMIN');
            }

            /**
             * @var $userManager UserManager
             */
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', 'Modifications enregistrées');
        }
        else {
            if($user->hasRole('ROLE_ADMIN')) {
                $form->get("admin")->setData(true);
            }
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
    public function registerUser(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->get("admin")->getData()) {
                $user->addRole('ROLE_ADMIN');
            }
            else {
                $user->removeRole('ROLE_ADMIN');
            }

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