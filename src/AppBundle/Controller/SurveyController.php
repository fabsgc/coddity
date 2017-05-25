<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Choice;
use AppBundle\Entity\Participant;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyChoices;
use AppBundle\Entity\SurveyGeneral;
use AppBundle\Entity\SurveyParticipants;
use AppBundle\Entity\User;
use AppBundle\Entity\Vote;
use AppBundle\Form\Type\SurveyChoicesChoiceType;
use AppBundle\Form\Type\SurveyChoicesDateType;
use AppBundle\Form\Type\SurveyGeneralType;
use AppBundle\Form\Type\SurveyParticipantsType;
use AppBundle\Repository\ChoiceRepository;
use AppBundle\Repository\ParticipantRepository;
use AppBundle\Repository\UserRepository;
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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session;

/**
 * @Route("/survey")
 */

class SurveyController extends Controller
{
	/**
	 * @Route("/", name="survey_home")
     * @Security("has_role('ROLE_USER')")
	 * @param Request $request
	 * @return Response
	 */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $surveys = $em->getRepository('AppBundle:Survey')->findByUser($this->getUser());

        return $this->render('AppBundle:Survey:index.html.twig', [
            'surveys' => $surveys
        ]);
    }

    /**
     * @Route("/{survey}", name="survey_show")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function showAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:Survey:edit.html.twig', []);
    }

    /**
     * @Route("/{survey}/{token}", name="survey_results")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @param Request $request
     * @param Survey $survey
     * @param String $token
     * @return Response
     */
    public function showResults(Request $request, Survey $survey, String $token)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:Survey:edit.html.twig', []);
    }

    /**
     * @Route("/edit/{survey}", name="survey_edit")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function editAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:Survey:edit.html.twig', []);
    }

    /**
     * @Route("/delete/{survey}", name="survey_delete")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
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
     * @Route("/new/choice/{survey}", name="survey_choice_new")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newChoiceAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:Survey:index.html.twig', []);
    }

    /**
     * @Route("/edit/choice/{choice}", name="survey_choice_edit")
     * @ParamConverter("choice", class="AppBundle:Choice")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Choice $choice
     * @return Response
     */
    public function editChoiceAction(Request $request, Choice $choice)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:Survey:index.html.twig', []);
    }

    /**
     * @Route("/delete/choice/{choice}", name="survey_choice_delete")
     * @ParamConverter("choice", class="AppBundle:Choice")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param Choice $choice
     * @return Response
     */
    public function deleteChoiceAction(Request $request, Choice $choice)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->redirectToRoute('admin_survey_list');
    }

    /**
     * @Route("/new/participant/{survey}", name="survey_participant_new")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function newParticipantAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('AppBundle:Survey:index.html.twig', []);
    }

    /**
     * @Route("/delete/{participant}", name="survey_participant_delete")
     * @ParamConverter("participant", class="AppBundle:Participant")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param Participant $participant
     * @return Response
     */
    public function deleteParticipantAction(Request $request, Participant $participant)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->redirectToRoute('admin_survey_list');
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
    public function answerSurveyAction(Request $request, Survey $survey, string $token)
    {
        $em = $this->getDoctrine()->getManager();
        $participant = $em->getRepository('AppBundle:Participant')->findBySurveyAndToken($survey, $token);
        $choices = $em->getRepository('AppBundle:Choice')->findBySurvey($survey);

        if($participant instanceof Participant) {
            if($this->getUser() == null || $this->getUser() == $participant->getUser()) {
                if($participant->isHasVoted() == false) {
                    /** @var Form $form */
                    $form = $this->createFormBuilder()->add('Envoyer', SubmitType::class)->getForm();
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $surveyChoices = $request->get('survey_choice');

                        //We check that the user has sent something
                        if($surveyChoices != null) {
                            //We check if the user has given the correct number of choices
                            if(!$survey->isMultiple() && is_array($surveyChoices) && count($surveyChoices) > 1) {
                                $form->addError(new FormError('Ce sondage ne peut avoir qu\'une seule réponse'));
                            }
                            else {
                                if(!is_array($surveyChoices)) {
                                    $surveyChoices = [$surveyChoices => $surveyChoices];
                                }

                                $isValid = true;

                                //We check if each choice is valid
                                foreach ($surveyChoices as $key => $surveyChoice) {
                                    $choice = $em->getRepository('AppBundle:Choice')->findByIdAndSurvey($key, $survey);

                                    if(!$choice instanceof Choice) {
                                        $form->addError(new FormError('Certains des choix que vous avez donnés ne sont pas valides'));
                                        $isValid = false;
                                        break;
                                    }
                                }

                                if($isValid) {
                                    $participant->setHasVoted(true);
                                    $em->persist($participant);
                                    $em->flush();

                                    foreach ($surveyChoices as $key => $surveyChoice) {
                                        $choice = $em->getRepository('AppBundle:Choice')->findByIdAndSurvey($key, $survey);

                                        $vote = new Vote();
                                        $vote->setSurvey($survey);
                                        $vote->setParticipant($participant);
                                        $vote->setChoice($choice);
                                        $em->persist($vote);
                                        $em->flush();
                                    }

                                    $this->addFlash('success', 'Merci d\'avoir pris le temps de répondre à ce sondage !');
                                    return $this->redirectToRoute('home');
                                }
                            }
                       }
                       else {
                           $form->addError(new FormError('Vous devez au moins choisir une réponse'));
                       }
                    }

                    return $this->render('AppBundle:Survey:answer.html.twig', [
                        'survey' => $survey,
                        'choices' => $choices,
                        'form' => $form->createView()
                    ]);
                }
                else {
                    $this->addFlash('danger', 'Vous avez déjà voté ici');
                    return $this->redirectToRoute('home');
                }
            }
            else {
                $this->addFlash('danger', 'Vous ne pouvez pas voter ici');
                return $this->redirectToRoute('home');
            }
        }
        else {
            $this->addFlash('danger', 'Vous ne pouvez pas voter ici');
            return $this->redirectToRoute('home');
        }
    }

	/**
	 * @Route("/create/general", name="survey_create_general")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
	 * @param Request $request
	 * @return Response
	 */
	public function createSurveyGeneralAction(Request $request)
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
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function createSurveyChoicesAction(Request $request)
    {
        /** @var Session\Session $session */
        $session = $this->get("session");

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

                    if($surveyGeneral->getType() == 'CHOICE') {
                        if($countEqualChoice == 1 && trim($choice1) != '') {
                            array_push($choicesTrim, $choice1);
                        }
                    }
                    else {
                        if($countEqualChoice == 1) {
                            /** @var $choice1 \DateTime */
                            array_push($choicesTrim, $choice1->format('d/m/Y'));
                        }
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
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function createSurveyParticipantsAction(Request $request)
    {
        /** @var Session\Session $session */
        $session = $this->get("session");

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine')->getManager();

        /** @var Mailer $mailer */
        $mailer = $this->container->get('app.mailer');

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

                    if($user instanceof User && $user != $this->getUser() && !in_array($user->getEmail(), $participantsTrim)) {
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

                $session->remove('surveyGeneral');

                $this->addFlash('success', 'Votre sondage a bien été créé, il ne vous reste plus qu\'à y répondre !');

                return $this->redirectToRoute('survey_answer', [
                    'survey' => $survey->getId(),
                    'token' => $participant->getToken()
                ]);
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
     * @Route("/autocomplete/user", name="survey_autocomplete_user")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function autocompleteProfessionalAction(Request $request)
    {
        $data = array();
        $term = trim(strip_tags($request->get('term')));

        /** @var UserRepository $em */
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
        $users = $em->findUsersByNameOrEmail($term);

        /** @var User $user */
        foreach ($users as $user) {
            if($user != $this->getUser()) {
                array_push($data, ['key' => $user->getUsername(), "value" => $user->getUsername()]);
            }
        }

        $response = new JsonResponse();
        $response->setData($data);

        return $response;
    }

    /**
     * @return string
     */
    private function getCsrfToken(){
        return $this->has('security.csrf.token_manager') ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue() : null;
    }
}
