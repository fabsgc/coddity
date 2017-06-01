<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Choice;
use AppBundle\Entity\Participant;
use AppBundle\Entity\Result;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyChoices;
use AppBundle\Entity\SurveyGeneral;
use AppBundle\Entity\SurveyParticipants;
use AppBundle\Entity\User;
use AppBundle\Entity\Vote;
use AppBundle\Form\Type\ChoiceChoiceType;
use AppBundle\Form\Type\ChoiceDateType;
use AppBundle\Form\Type\ParticipantType;
use AppBundle\Form\Type\SurveyChoicesChoiceType;
use AppBundle\Form\Type\SurveyChoicesDateType;
use AppBundle\Form\Type\SurveyNoVotesType;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Form\Type\SurveyGeneralType;
use AppBundle\Form\Type\SurveyParticipantsType;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\Mailer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        if($survey->getUser() == $this->getUser()) {
            $choices = $em->getRepository('AppBundle:Choice')->findBySurvey($survey);
            $participants = $em->getRepository('AppBundle:Participant')->findBySurvey($survey);
            $votes = $em->getRepository('AppBundle:Vote')->findBySurvey($survey);
            $results = $em->getRepository('AppBundle:Result')->findBySurvey($survey);
            $votesCount = $em->getRepository('AppBundle:Vote')->countBySurvey($survey);
            $participantUser = $em->getRepository('AppBundle:Participant')->findBySurveyAndUser($survey, $this->getUser());

            return $this->render('AppBundle:Survey:show.html.twig', [
                'survey' => $survey,
                'choices' => $choices,
                'participants' => $participants,
                'votes' => $votes,
                'results' => $results,
                'votesCount' => $votesCount,
                'participantUser' => $participantUser
            ]);
        }

        $this->addFlash('error', 'Vous ne pouvez pas voir ce sondage.');
        $this->redirectToRoute('home');
    }

    /**
     * @Route("/results/{survey}/{token}", name="survey_results")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @param Request $request
     * @param Survey $survey
     * @param String $token
     * @return Response
     */
    public function showResults(Request $request, Survey $survey, String $token)
    {
        $em = $this->getDoctrine()->getManager();
        $participant = $em->getRepository('AppBundle:Participant')->findBySurveyAndToken($survey, $token);

        if(!$survey->isOpened() && $participant instanceof Participant) {
            $results = $em->getRepository('AppBundle:Result')->findBySurvey($survey);

            return $this->render('AppBundle:Survey:results.html.twig', [
                'survey' => $survey,
                'results' => $results
            ]);
        }

        $this->addFlash('error', 'Vous ne pouvez pas voir les résultats de ce sondage.');
        $this->redirectToRoute('home');
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
        if($survey->getUser() == $this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $votesCount = $em->getRepository('AppBundle:Vote')->countBySurvey($survey);

            if($votesCount > 0) {
                $form = $this->createForm(SurveyNoVotesType::class, $survey);
            }
            else {
                $form = $this->createForm(SurveyType::class, $survey);
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $survey->setUpdatedAt(new \DateTime());
                $em->persist($survey);
                $em->flush();

                $this->addFlash('success', 'Le sondage a bien été édité');
                return $this->redirectToRoute('survey_show', ['survey' => $survey->getId()]);
            }
            else {
                return $this->render('AppBundle:Survey:edit.html.twig', [
                    'survey' => $survey,
                    'form' => $form->createView()
                ]);
            }
        }

        $this->addFlash('error', 'Vous ne pouvez pas éditer ce sondage.');
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
            $this->addFlash('success', 'Sondage supprimé.');
        }
        else {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer ce sondage.');
        }

        return $this->redirectToRoute('survey_home');
    }

    /**
     * @Route("/end/{survey}", name="survey_end")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function endAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        $votesCount = $em->getRepository('AppBundle:Vote')->countBySurvey($survey);

        if($this->getUser() != $survey->getUser() || !$survey->isOpened() || $votesCount == 0) {
            $this->addFlash('danger', 'Vous ne pouvez pas mettre fin à ce sondage.');
            return $this->redirectToRoute('survey_show', ['survey' => $survey->getId()]);
        }

        $choices = $em->getRepository('AppBundle:Choice')->findBySurvey($survey);
        $votes = $em->getRepository('AppBundle:Vote')->findBySurvey($survey);

        //For each choice, we calculate his score
        $choicesScores = [];

        /** @var Choice $choice */
        foreach ($choices as $choice) {
            $choicesScores[$choice->getId()] = 0;

            /** @var Vote $vote */
            foreach ($votes as $vote) {
                if ($vote->getChoice() === $choice) {
                    $choicesScores[$choice->getId()]++;
                }
            }
        }

        //Then we look for the highest score
        $maxScore = max($choicesScores);

        //If it's different from 0, we check if there are several choices with same score
        $choicesWithHighestScore = [];

        if($maxScore != 0) {
            foreach ($choicesScores as $key => $choicesScore) {
                if($choicesScore == $maxScore) {
                    array_push($choicesWithHighestScore, $key);
                }
            }
        }

        //We save all the results
        $survey->setOpened(false);
        $em->persist($survey);
        $em->flush();

        foreach ($choicesScores as $key => $choicesScore) {
            /** @var Choice $choice */
            $choice = $em->getRepository('AppBundle:Choice')->find($key);

            $result = new Result();
            $result->setSurvey($survey);
            $result->setValue($choicesScore / $votesCount * 100);
            $result->setChoice($choice);
            $em->persist($result);
            $em->flush();
        }

        //If there are several choices with same score, we must resolve a conflit
        if(count($choicesWithHighestScore) > 1) {
            /** @var Choice[] $choicesEntitiesSameScores */
            $choicesEntitiesSameScores = [];

            foreach ($choicesWithHighestScore as $value) {
                array_push($choicesEntitiesSameScores, $em->getRepository('AppBundle:Choice')->find($value));
            }

            return $this->render('AppBundle:Survey:end.html.twig', [
                'survey' => $survey,
                'choicesEntitiesSameScores' => $choicesEntitiesSameScores
            ]);
        }
        else {
            $survey->setWinner($em->getRepository('AppBundle:Choice')->find($choicesWithHighestScore[0]));
            $em->persist($survey);
            $em->flush();

            $this->sendMailEndSurvey($survey);
            $this->addFlash('success', 'Le sondage s\'est terminé avec succès !');
        }

        return $this->redirectToRoute('survey_show', ['survey' => $survey->getId()]);
    }

    /**
     * @Route("/winner/{choice}", name="survey_winner")
     * @ParamConverter("choice", class="AppBundle:Choice")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param Choice $choice
     * @return Response
     */
    public function winnerAction(Request $request, Choice $choice)
    {
        $em = $this->getDoctrine()->getManager();
        $survey = $choice->getSurvey();

        if($this->getUser() != $survey->getUser() || $survey->isOpened()) {
            $this->addFlash('danger', 'Vous ne pouvez pas définir de gagnant pour ce sondage.');
            return $this->redirectToRoute('survey_show', ['survey' => $survey->getId()]);
        }

        $this->sendMailEndSurvey($survey);

        $survey->setWinner($choice);
        $em->persist($survey);
        $em->flush();

        $this->addFlash('success', 'Le choix a bien été défini comme gagnant du sondage.');
        return $this->redirectToRoute('survey_show', ['survey' => $survey->getId()]);
    }

    private function sendMailEndSurvey(Survey $survey) {
        $em = $this->getDoctrine()->getManager();

        $participants = $em->getRepository('AppBundle:Participant')->findBySurvey($survey);

        /** @var Participant $participant */
        foreach ($participants as $participant) {
            if($participant->getUser() != $this->getUser()) {
                $mailer = $this->container->get('app.mailer');
                $mailer->sendToEmail($participant->getEmail(), 'Résultat du sondage "' . $survey->getName() . '"', 'Survey/end_survey', [
                    'token' => $participant->getToken(), 'survey' => $survey]
                );
            }
        }
    }

    /**
     * @Route("/conflict/generate/{survey}", name="survey_conflict_resolve")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param Survey $survey
     * @return Response
     * @internal param Choice $choice
     */
    public function conflictResolveAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser() != $survey->getUser() || $survey->isOpened()) {
            $this->addFlash('danger', 'Vous ne pouvez pas créer de nouveau sondage à partir de celui-ci.');
            return $this->redirectToRoute('survey_show', ['survey' => $survey->getId()]);
        }

        $newSurvey = new Survey();
        $newSurvey->setUser($this->getUser());

        //Save survey
        $newSurvey = new Survey();
        $newSurvey->setUser($this->getUser());
        $newSurvey->setName($survey->getName());
        $newSurvey->setDescription($survey->getDescription());
        $newSurvey->setType($survey->getType());
        $newSurvey->setMultiple($survey->isMultiple());
        $em->persist($newSurvey);
        $em->flush();

        //Save choices
        $results = $em->getRepository('AppBundle:Result')->findBySurvey($survey);
        $maxScore = 0;

        /** @var Result $result */
        foreach ($results as $key => $result) {
            if($key == 0) {
                $maxScore = $result->getValue();
            }

            if($result->getValue() >= $maxScore) {
                $choice = $result->getChoice();
                $newChoice = new Choice();
                $newChoice->setDescription($choice->getDescription());
                $newChoice->setSurvey($newSurvey);
                $em->persist($newChoice);
                $em->flush();
            }
            else {
                break;
            }
        }

        $tokenUser = '';
        $participants = $em->getRepository('AppBundle:Participant')->findBySurvey($survey);

        /** @var Participant $participant */
        foreach ($participants as $participant) {
            $tokenGenerator = $this->get('fos_user.util.token_generator');

            $newParticipant = new Participant();
            $newParticipant->setHasVoted(false);
            $newParticipant->setSurvey($newSurvey);
            $newParticipant->setToken($tokenGenerator->generateToken());
            $newParticipant->setUser($participant->getUser());
            $newParticipant->setEmail($participant->getEmail());

            if($participant->getUser() === $this->getUser()) {
                $tokenUser = $newParticipant->getToken();
            }
            else {
                $mailer = $this->container->get('app.mailer');
                $mailer->sendToEmail($participant->getEmail(), 'Participez à un nouveau sondage !', 'Survey/new_survey', [
                    'token' => $participant->getToken(),
                    'survey' => $survey
                ]);
            }

            $em->persist($newParticipant);
            $em->flush();
        }

        $this->addFlash('success', 'Un nouveau sondage vient d\'être créé à partir des choix restants. Vous pouvez dès maintenant y répondre');

        return $this->redirectToRoute('survey_answer', [
            'survey' => $newSurvey->getId(),
            'token' => $tokenUser
        ]);
    }

    /**
     * @Route("/new/choice/{survey}", name="survey_choice_new")
     * @ParamConverter("survey", class="AppBundle:Survey")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Survey $survey
     * @return Response
     */
    public function newChoiceAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        $votesCount = $em->getRepository('AppBundle:Vote')->countBySurvey($survey);

        if($this->getUser() == $survey->getUser() && $survey->isOpened() && $votesCount == 0) {
            $choices = $em->getRepository('AppBundle:Choice')->findBySurvey($survey);
            $choice = new Choice();
            $choice->setSurvey($survey);

            if($survey->getType() == 'CHOICE') {
                $form = $this->createForm(ChoiceChoiceType::class, $choice);
            }
            else {
                $form = $this->createForm(ChoiceDateType::class, $choice);
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if($survey->getType() == 'CHOICE') {
                    $choiceText = $choice->getDescription();
                }
                else {
                    /** @var \DateTime $choiceDate */
                    $choiceDate = $form->get('description')->getData();
                    $choiceText = $choiceDate->format('d/m/Y');
                }

                /** @var Choice $choiceExistant */
                foreach ($choices as $choiceWichExist) {
                    if($choiceWichExist->getDescription() == $choiceText) {
                        $form->addError(new FormError('Ce choix existe déjà.'));

                        return $this->render('AppBundle:Survey:newChoice.html.twig', [
                            'survey' => $survey,
                            'form' => $form->createView(),
                            'csrf_token' => $this->getCsrfToken()
                        ]);
                    }
                }

                $choice->setDescription($choiceText);

                $em->persist($choice);
                $em->flush();

                $this->addFlash('success', 'Le choix a bien été ajouté.');
                return $this->redirectToRoute('survey_show', ['survey' => $choice->getSurvey()->getId()]);
            }
            else {
                return $this->render('AppBundle:Survey:newChoice.html.twig', [
                    'survey' => $survey,
                    'form' => $form->createView(),
                    'csrf_token' => $this->getCsrfToken()
                ]);
            }
        }
        else {
            $this->addFlash('error', 'Vous ne pouvez ajouter de choix à ce sondage');
            return $this->redirectToRoute('survey_home');
        }
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
        $survey = $choice->getSurvey();
        $em = $this->getDoctrine()->getManager();
        $votesCount = $em->getRepository('AppBundle:Vote')->countBySurvey($survey);

        if($this->getUser() == $survey->getUser() && $survey->isOpened() && $votesCount == 0) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($choice);
            $em->flush();
            $this->addFlash('success', 'Participant supprimé');
        }
        else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce choix');
        }

        return $this->redirectToRoute('survey_show', ['survey' => $choice->getSurvey()->getId()]);
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

        if($this->getUser() == $survey->getUser() && $survey->isOpened()) {
            $participant = new Participant();
            $participant->setSurvey($survey);
            $form = $this->createForm(ParticipantType::class, $participant);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Check if it's a valid user or an email and retrieve email
                $email = '';

                $user = $em->getRepository('AppBundle:User')->findByEmailOrUsername($form->get('participant')->getData());

                if($user instanceof User) {
                    $email = $user->getEmail();
                    $participant->setUser($user);
                }
                else {
                    if(filter_var($form->get('participant')->getData(), FILTER_VALIDATE_EMAIL)) {
                        $email = $form->get('participant')->getData();
                    }
                    else {
                        $form->addError(new FormError('Vous devez indiquer un nom d\'utilisateur valide ou une adresse email.'));
                    }
                }

                //Check if that email does not already exist for that survey
                $userAlreadyExist = $em->getRepository('AppBundle:Participant')->countBySurveyAndEmail($survey, $email);

                if($userAlreadyExist == 0 && $form->isValid()) {
                    $tokenGenerator = $this->get('fos_user.util.token_generator');

                    $participant->setToken($tokenGenerator->generateToken());
                    $participant->setEmail($email);
                    $em->persist($participant);
                    $em->flush();

                    $mailer = $this->container->get('app.mailer');

                    if($user instanceof User) {
                        $mailer->sendToUser($user, 'Participez à un nouveau sondage !', 'Survey/new_survey', [
                            'token' => $participant->getToken(),
                            'survey' => $survey
                        ]);
                    }
                    else {
                        $mailer->sendToEmail($email, 'Participez à un nouveau sondage !', 'Survey/new_survey', [
                            'token' => $participant->getToken(),
                            'survey' => $survey
                        ]);
                    }

                    $this->addFlash('success', 'Le participant a bien été ajouté.');
                    return $this->redirectToRoute('survey_show', ['survey' => $participant->getSurvey()->getId()]);
                }
                else {
                    if($form->isValid()) {
                        $form->addError(new FormError('Cet utilisateur participe déjà au sondage'));
                    }
                }
            }

            return $this->render('AppBundle:Survey:newParticipant.html.twig', [
                'form' => $form->createView(),
                'survey' => $survey,
                'csrf_token' => $this->getCsrfToken()
            ]);
        }
        else {
            $this->addFlash('error', 'Vous ne pouvez ajouter de participant à ce sondage');
            return $this->redirectToRoute('survey_home');
        }
    }

    /**
     * @Route("/delete/participant/{participant}", name="survey_participant_delete")
     * @ParamConverter("participant", class="AppBundle:Participant")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param Participant $participant
     * @return Response
     */
    public function deleteParticipantAction(Request $request, Participant $participant)
    {
        if($this->getUser() == $participant->getSurvey()->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($participant);
            $em->flush();
            $this->addFlash('success', 'Participant supprimé');
        }
        else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce participant');
        }

        return $this->redirectToRoute('survey_show', ['survey' => $participant->getSurvey()->getId()]);
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

                //Check if email addresses or users exist and if they are finally at least one existing participant

                foreach ($participantsTrim as $participant) {
                    /** @var User $participantEntity */
                    $user = $em->getRepository('AppBundle:User')->findByEmailOrUsername($participant);

                    if($user instanceof User && $user != $this->getUser() && !in_array($user->getEmail(), $participantsTrim)) {
                        array_push($participantsExistTrim, $user);
                    }
                    else {
                        if(filter_var($participant, FILTER_VALIDATE_EMAIL) && $participant != $this->getUser()->getEmail()) {
                            array_push($participantsExistTrim, $participant);
                        }

                        if($participant == $this->getUser()->getEmail() || $participant == $this->getUser()->getUsername()) {
                            $form->addError(new FormError('Vous ne pouvez pas vous ajouter au sondage car c\'est automatique'));
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

                $tokenGenerator = $this->get('fos_user.util.token_generator');

                //Save survey
                $survey = new Survey();
                $survey->setUser($this->getUser());
                $survey->setName($surveyGeneral->getName());
                $survey->setDescription($surveyGeneral->getDescription());
                $survey->setType($surveyGeneral->getType());
                $survey->setType($surveyGeneral->getType());
                $survey->setMultiple($surveyGeneral->isMultiple());
                $em->persist($survey);
                $em->flush();

                //Save choices
                foreach ($surveyChoices->getChoices() as $surveyChoice) {
                    $choice = new Choice();
                    $choice->setDescription($surveyChoice);
                    $choice->setSurvey($survey);
                    $em->persist($choice);
                    $em->flush();
                }

                //We add the creator to the survey
                $participantUser = new Participant();
                $participantUser->setHasVoted(false);
                $participantUser->setSurvey($survey);
                $participantUser->setToken($tokenGenerator->generateToken());
                $participantUser->setUser($this->getUser());
                $participantUser->setEmail($this->getUser()->getEmail());
                $em->persist($participantUser);
                $em->flush();

                //Save participants + send email
                foreach ($participantsExistTrim as $surveyParticipant) {
                    $participant = new Participant();
                    $participant->setHasVoted(false);
                    $participant->setSurvey($survey);
                    $participant->setToken($tokenGenerator->generateToken());

                    if($surveyParticipant instanceof User) {
                        $participant->setUser($surveyParticipant);
                        $participant->setEmail($surveyParticipant->getEmail());
                    }
                    else {
                        $participant->setEmail($surveyParticipant);
                    }

                    $mailer->sendToEmail($participant->getEmail(), 'Participez à un nouveau sondage !', 'Survey/new_survey', [
                        'token' => $participant->getToken(),
                        'survey' => $survey
                    ]);

                    $em->persist($participant);
                    $em->flush();
                }

                $session->remove('surveyGeneral');

                $this->addFlash('success', 'Votre sondage a bien été créé, il ne vous reste plus qu\'à y répondre !');

                return $this->redirectToRoute('survey_answer', [
                    'survey' => $survey->getId(),
                    'token' => $participantUser->getToken()
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
