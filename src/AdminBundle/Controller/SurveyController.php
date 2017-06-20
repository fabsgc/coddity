<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\SurveyType;
use AppBundle\Entity\Survey;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findAll();

        return $this->render('AdminBundle:Survey:index.html.twig', array(
            'surveys' => $surveys
        ));
    }

    /**
     * @Route("/edit/{survey}", name="admin_survey_edit")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function editAction(Request $request, Survey $survey)
    {
        /**
         * @var $em EntityManager
         */
        $em = $this->container->get('doctrine')->getManager();

        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())  {
            $em->persist($survey);
            $em->flush($survey);

            $this->addFlash('success', 'Modifications enregistrées');
        }

        return $this->render('AdminBundle:Survey:edit.html.twig', array(
            'survey' => $survey,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/see/{survey}", name="admin_survey_see")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function seeAction(Request $request, Survey $survey)
    {
        $participants = $this->getDoctrine()->getRepository('AppBundle:Participant')->findBySurvey($survey);
        $choices = $this->getDoctrine()->getRepository('AppBundle:Choice')->findBySurvey($survey);
        $votes = $this->getDoctrine()->getRepository('AppBundle:Vote')->findBySurvey($survey);
        $results = $this->getDoctrine()->getRepository('AppBundle:Result')->findBySurvey($survey);

        return $this->render('AdminBundle:Survey:see.html.twig', array(
            'survey' => $survey,
            'participants' => $participants,
            'choices' => $choices,
            'votes' => $votes,
            'results' => $results
        ));
    }

    /**
     * @Route("/delete/{survey}", name="admin_survey_delete")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Method({"DELETE", "GET"})
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function deleteAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($survey);
        $em->flush();
        $this->addFlash('success', 'Sondage supprimé');
        return $this->redirectToRoute('admin_survey_list');
    }
}