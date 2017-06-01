<?php

namespace UserBundle\Controller;

use AppBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use UserBundle\Form\Type\UserSettingsType;

class UserController extends Controller {
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/account/edit/general", name="profile_edit_general")
     * @param Request $request
     * @return Response
     */
    public function editGeneralAction(Request $request) {
        $user = $this->getUser();

        $form = $this->createForm(UserSettingsType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', 'Votre profil a été mis à jour');

            return $this->redirectToRoute('profile_edit_general');
        }

        return $this->render('UserBundle:Profile:edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/account/edit/parameters", name="profile_edit_parameters")
     * @param Request $request
     * @return Response
     */
    public function editParametersAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Form $form */
        $form = $this->createFormBuilder()
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' =>  'Les mots de passe ne correspondent pas.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options' => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Confirmation du nouveau mot de passe'),
                'mapped' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Enregistrer'
            ))->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $postData = current($request->request->all());
            $user->setPlainPassword($postData['plainPassword']['first']);

            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', 'Vos paramètres ont été mis à jour');

            return $this->redirectToRoute('profile_edit_parameters');
        }

        return $this->render('UserBundle:Profile:editParameters.html.twig', [
            'form' => $form->createView()
        ]);
    }
}