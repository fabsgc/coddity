<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Professional;
use AppBundle\Entity\User;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
use UserBundle\Form\Type\ChangePasswordType;
use UserBundle\Form\Type\ProfessionalSettingsType;
use UserBundle\Form\Type\ProfessionalType;
use UserBundle\Form\Type\UserSettingsType;
use UserBundle\Form\Type\UserType;

class UserController extends Controller
{
    /**
     * Show user profile
     * @Route("/account/{slug}", name="fos_user_profile_show")
     * @Method({"GET"})
     * @ParamConverter("user", class="AppBundle:User")
     * @param Request $request
     * @param User $profile
     * @return Response
     */
    public function showProfileAction(Request $request, User $profile)
    {
        if ($profile instanceof Professional) {
            $backUrl = '';

            if($request->query->get('back') != null){
                $session = $this->get("session");
                $backUrl = $session->get('back-url');
            }

            $mark = $this->getDoctrine()->getManager()->getRepository('AppBundle:Professional')->getMark($profile);
            $markNumber = $this->getDoctrine()->getManager()->getRepository('AppBundle:Professional')->countMarksByProfessional($profile);
            $marks = $this->getDoctrine()->getManager()->getRepository('AppBundle:Professional')->getMarkCriterias($profile);

            return $this->render('UserBundle:Profile:show.html.twig', compact('profile', 'mark', 'markNumber', 'backUrl', 'marks'));
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/account/edit/general", name="profile_edit_general")
     * @param Request $request
     * @return Response
     */
    public function editGeneralAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $user->getLocation()->setNewLocation(
            ($user->getLocation()->getStreet() != '' ? $user->getLocation()->getStreet() . ', ' : '') .
            ($user->getLocation()->getPostalCode() != '' ? $user->getLocation()->getPostalCode() . ', ' : '') .
            ($user->getLocation()->getCity() != '' ? $user->getLocation()->getCity() . ', ' : '') .
            ($user->getLocation()->getCountry() != '' ? $user->getLocation()->getCountry() : '')
        );

        if($user->getLocation()->getNewLocation() == ',  , ') {
            $user->getLocation()->setNewLocation('');
        }

        if ($user instanceof Professional)
        {
            $form = $this->createForm(ProfessionalType::class, $user);
        }
        else
        {
            $form = $this->createForm(UserType::class, $user);
        }

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $user->getPictureUpload();

            if ($user instanceof Professional) {
                if ($user->getPaymentMethod()->getIban() != '' || $user->getPaymentMethod()->getBic() != '') {
                    $user->getPaymentMethod()->setMethod('DIRECT_DEBIT');
                }
                else if ($user->getPaymentMethod()->getIban() == '' || $user->getPaymentMethod()->getBic() == '') {
                    $user->getPaymentMethod()->setMethod('');
                }

                if($user->getProfession() != 'Avocat') {
                    $user->setSpecialization('');
                }
            }

            if ($file != null) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );

                $user->setPicture($fileName);
            }

            if($user instanceof Customer) {
                if($user->getLocation()->getNewLocation() == '') {
                    $user->getLocation()->setStreet('');
                    $user->getLocation()->setPostalCode('');
                    $user->getLocation()->setCity('');
                    $user->getLocation()->setCountry('');
                }
            }

            $user->getLocation()->setStreet(trim(str_replace('null', '', $user->getLocation()->getStreet())));
            $user->getLocation()->setPostalCode(trim(str_replace('null', '', $user->getLocation()->getPostalCode())));
            $user->getLocation()->setCity(trim(str_replace('null', '', $user->getLocation()->getCity())));
            $user->getLocation()->setCountry(trim(str_replace('null', '', $user->getLocation()->getCountry())));

            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', 'Votre profil a été mis à jour');

            return $this->redirectToRoute('profile_edit_general');
        }

        return $this->render('UserBundle:Profile:editGeneral.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/account/edit/parameters", name="profile_edit_parameters")
     * @param Request $request
     * @return Response
     */
    public function editParametersAction(Request $request)
    {
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

        if($form->isSubmitted() && $form->isValid())
        {
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

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/account/edit/preferences", name="profile_edit_preferences")
     * @param Request $request
     * @return Response
     */
    public function editPreferencesAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfessionalSettingsType::class, $user->getSettings());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $this->addFlash('success', 'Vos préférences ont été mis à jour');

            return $this->redirectToRoute('profile_edit_preferences');
        }

        return $this->render('UserBundle:Profile:editPreferences.html.twig', [
            'form' => $form->createView()
        ]);
    }
}