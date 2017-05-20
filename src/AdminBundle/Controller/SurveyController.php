<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
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
 * @Route("/survey")
 * @Security("has_role('ROLE_ADMIN')")
 */
class SurveyController extends Controller
{

    /**
     * @Route("/list", name="admin_survey_list")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        /**
         * @var $userRepo UserRepository
         */
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render('AdminBundle:Survey:index.html.twig', array(
            'users' => $users
        ));
    }
}