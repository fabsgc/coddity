<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Choice;
use AppBundle\Entity\Participant;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyChoices;
use AppBundle\Entity\SurveyGeneral;
use AppBundle\Entity\SurveyParticipants;
use AppBundle\Entity\User;
use AppBundle\Form\Type\SurveyChoicesChoiceType;
use AppBundle\Form\Type\SurveyChoicesDateType;
use AppBundle\Form\Type\SurveyGeneralType;
use AppBundle\Form\Type\SurveyParticipantsType;
use AppBundle\Service\Mailer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Form\FormError;
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
     * @Route("/answer/{survey}/{token}", name="survey_answer")
     * @Method({"GET", "POST"})
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @param Request $request
     * @param Survey $survey
     * @param string $token
     * @return Response
     */
    public function answerSurvey(Request $request, Survey $survey, string $token)
    {
        $em = $this->getDoctrine()->getManager();
        $participant = $em->getRepository('AppBundle:Participant')->findBySurveyAndToken($survey, $token);

        if($participant instanceof Participant) {
            if($participant->isHasVoted() == false) {
                return $this->render('AppBundle:Survey:answer.html.twig', [
                    'survey' => $survey
                ]);
            }
            else {
                $this->addFlash('error', 'Vous avez déjà voté ici');
            }
        }
        else {
            $this->addFlash('error', 'Vous ne pouvez pas voter ici');
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/delete/{id}", name="survey_delete")
     * @ParamConverter("survey", class="AppBundle:Support")
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
                //Check number of choices
                //Check that they are different

                $choices = $surveyChoices->getChoices();
                $choicesTrim = [];

                foreach ($choices as $choice1) {
                    $countEqualChoice = 0;

                    foreach ($choices as $choice2) {
                        if($choice1 == $choice2) {
                            $countEqualChoice++;
                        }
                    }

                    if($countEqualChoice == 1 && trim($choice1) != '') {
                        array_push($choicesTrim, $choice1);
                    }
                }

                if (count($choicesTrim) < 2) {
                    $form->addError(new FormError('Il doit y avoir au moins deux choix différents'));

                    return $this->render('AppBundle:Survey:createSurveyChoices.html.twig', [
                        'form' => $form->createView(),
                        'csrf_token' => $this->getCsrfToken()
                    ]);
                }
                else {
                    $surveyChoices->setChoices($choicesTrim);
                }

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

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine')->getManager();

        /** @var Mailer $mailer */
        $mailer = $this->container->get('app.mailer');

        /*if ($session->has('surveyParticipants')) {
            $surveyParticipants = $session->get('surveyParticipants');
        }
        else {
            $surveyParticipants = new SurveyParticipants();
        }*/

        $surveyParticipants = new SurveyParticipants();

        if ($session->has('surveyChoices')) {
            $form = $this->createForm(SurveyParticipantsType::class, $surveyParticipants);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Check number of participants
                //Check if participants are different

                $participants = $surveyParticipants->getParticipants();
                $participantsTrim = [];
                $participantsExistTrim = [];

                foreach ($participants as $participant1) {
                    $countEqualParticipant = 0;

                    foreach ($participants as $participant2) {
                        if($participant1 == $participant2) {
                            $countEqualParticipant++;
                        }
                    }

                    if($countEqualParticipant == 1 && trim($participant1) != '') {
                        array_push($participantsTrim, $participant1);
                    }
                }

                if (count($participantsTrim) < 1) {
                    $form->addError(new FormError('Il doit y avoir au moins un participant'));

                    return $this->render('AppBundle:Survey:createSurveyParticipants.html.twig', [
                        'form' => $form->createView(),
                        'csrf_token' => $this->getCsrfToken()
                    ]);
                }

                //Check if email adress or users exist and if they are finally at least one existing participant

                foreach ($participantsTrim as $participant) {
                    /** @var User $participantEntity */
                    $user = $em->getRepository('AppBundle:User')->findByEmailOrUsername($participant);

                    if($user instanceof User && $user != $this->getUser()) {
                        array_push($participantsExistTrim, $user);
                    }
                    else {
                        if(filter_var($participant, FILTER_VALIDATE_EMAIL)) {
                            array_push($participantsExistTrim, $participant);
                        }
                    }
                }

                if (count($participantsExistTrim) < 1) {
                    $form->addError(new FormError('Il doit y avoir au moins un participant'));

                    return $this->render('AppBundle:Survey:createSurveyParticipants.html.twig', [
                        'form' => $form->createView(),
                        'csrf_token' => $this->getCsrfToken()
                    ]);
                }

                /** @var SurveyGeneral $surveyGeneral */
                $surveyGeneral = $session->get('surveyGeneral');
                /** @var SurveyChoices $surveyChoices */
                $surveyChoices = $session->get('surveyChoices');

                //Save survey
                $survey = new Survey();
                $survey->setName($surveyGeneral->getName());
                $survey->setDescription($surveyGeneral->getDescription());
                $survey->setType($surveyGeneral->getType());
                $survey->setType($surveyGeneral->getType());
                $survey->setMultiple($surveyGeneral->isMultiple());
                $em->persist($survey);
                $em->flush();

                $order = 0;

                //Save choices
                foreach ($surveyChoices->getChoices() as $surveyChoice) {
                    $choice = new Choice();
                    $choice->setDescription($surveyChoice);
                    $choice->setSurvey($survey);
                    $choice->setOrdering($order);
                    $em->persist($choice);
                    $em->flush();

                    $order++;
                }

                //Save participants + send email
                foreach ($participantsExistTrim as $surveyParticipant) {
                    $participant = new Participant();
                    $participant->setHasVoted(false);
                    $participant->setSurvey($survey);
                    $participant->setToken(uniqid('', true));

                    if($surveyParticipant instanceof User) {
                        $participant->setUser($surveyParticipant);

                        $mailer->sendToUser($surveyParticipant, 'Participez à un nouveau sondage !', 'Survey/new_survey', [
                            'token' => $participant->getToken(),
                            'survey' => $survey
                        ]);
                    }
                    else {
                        $participant->setEmail($surveyParticipant);

                        $mailer->sendToEmail($surveyParticipant, 'Participez à un nouveau sondage !', 'Survey/new_survey', [
                            'token' => $participant->getToken(),
                            'survey' => $survey
                        ]);
                    }

                    $em->persist($participant);
                    $em->flush();
                }

                //We add the creator to the survey
                $participant = new Participant();
                $participant->setHasVoted(false);
                $participant->setSurvey($survey);
                $participant->setToken(uniqid('', true));
                $participant->setUser($this->getUser());
                $em->persist($participant);
                $em->flush();

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
