<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyChoices;
use AppBundle\Entity\SurveyGeneral;
use AppBundle\Entity\SurveyParticipants;
use AppBundle\Form\Type\SurveyChoicesChoiceType;
use AppBundle\Form\Type\SurveyChoicesDateType;
use AppBundle\Form\Type\SurveyGeneralType;
use AppBundle\Form\Type\SurveyParticipantsType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session;

/**
 * @Route("/survey")
 * @Security("has_role('ROLE_USER')")
 */

class SurveyController extends Controller
{
	/**
	 * @Route("/", name="survey_home")
	 * @param Request $request
	 * @return Response
	 */
    public function listAction(Request $request)
    {
        return $this->render('AppBundle:Survey:index.html.twig', []);
    }

    /**
     * @Route("/edit", name="survey_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function editSurvey(Request $request)
    {
        return $this->render('AppBundle:Survey:index.html.twig', []);
    }

    /**
     * @Route("/delete/{id}", name="survey_delete")
     * @ParamConverter("support", class="AppBundle:Support")
     * @Method({"GET"})
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function deleteAction(Request $request, Survey $survey)
    {
        if($this->getUser() == $survey->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($survey);
            $em->flush();
            $this->addFlash('success', 'Sondage supprimé');
        }
        else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce sondage');
        }

        return $this->redirectToRoute('admin_survey_list');
    }

	/**
	 * @Route("/create/general", name="survey_create_general")
     * @Method({"GET", "POST"})
	 * @param Request $request
	 * @return Response
	 */
	public function createSurveyGeneral(Request $request)
	{
        /** @var Session\Session $session */
        $session = $this->get("session");

        if ($session->has('surveyGeneral')) {
            $surveyGeneral = $session->get('surveyGeneral');
        }
        else {
            $surveyGeneral = new surveyGeneral();
        }

        $form = $this->createForm(SurveyGeneralType::class, $surveyGeneral);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('surveyGeneral', $surveyGeneral);
            return $this->redirectToRoute('survey_create_choices');
        }
        else {
            return $this->render('AppBundle:Survey:createSurveyGeneral.html.twig', [
                'form' => $form->createView(),
                'csrf_token' => $this->getCsrfToken()
            ]);
        }
	}

    /**
     * @Route("/create/choices", name="survey_create_choices")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function createSurveyChoices(Request $request)
    {
        /** @var Session\Session $session */
        $session = $this->get("session");

        /*if ($session->has('surveyChoices')) {
            $surveyChoices = $session->get('surveyChoices');
        }
        else {
            $surveyChoices = new surveyChoices();
        }*/

        $surveyChoices = new surveyChoices();

        if ($session->has('surveyGeneral')) {
            /** @var SurveyGeneral $surveyGeneral */
            $surveyGeneral = $session->get('surveyGeneral');

            if($surveyGeneral->getType() == 'CHOICE') {
                $form = $this->createForm(SurveyChoicesChoiceType::class, $surveyChoices);
            }
            else {
                $form = $this->createForm(SurveyChoicesDateType::class, $surveyChoices);
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $session->set('surveyChoices', $surveyChoices);
                return $this->redirectToRoute('survey_create_participants');
            }
            else {
                return $this->render('AppBundle:Survey:createSurveyChoices.html.twig', [
                    'form' => $form->createView(),
                    'csrf_token' => $this->getCsrfToken()
                ]);
            }
        }
        else {
            $this->addFlash('danger', 'Vous devez valider les étapes précédentes du formulaire.');
            return $this->redirectToRoute('survey_create_general');
        }
    }

    /**
     * @Route("/create/participants", name="survey_create_participants")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function createSurveyParticipants(Request $request)
    {
        /** @var Session\Session $session */
        $session = $this->get("session");

        /*if ($session->has('surveyParticipants')) {
            $surveyParticipants = $session->get('surveyParticipants');
        }
        else {
            $surveyParticipants = new SurveyParticipants();
        }*/

        $surveyParticipants = new SurveyParticipants();

        if ($session->has('surveyGeneral')) {
            $form = $this->createForm(SurveyParticipantsType::class, $surveyParticipants);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                var_dump($surveyParticipants->getParticipants());

                //return $this->redirectToRoute('survey_create_participants');
            }
            else {
                return $this->render('AppBundle:Survey:createSurveyParticipants.html.twig', [
                    'form' => $form->createView(),
                    'csrf_token' => $this->getCsrfToken()
                ]);
            }
        }
        else {
            $this->addFlash('danger', 'Vous devez valider les étapes précédentes du formulaire.');
            return $this->redirectToRoute('survey_create_general');
        }
    }

    /**
     * @return string
     */
    private function getCsrfToken(){
        return $this->has('security.csrf.token_manager') ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue() : null;
    }
}
