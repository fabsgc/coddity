<?php

namespace UserBundle\DataFixtures\ORM;

use AppBundle\Entity\Choice;
use AppBundle\Entity\Participant;
use AppBundle\Entity\Survey;
use AppBundle\Entity\User;
use AppBundle\Entity\Vote;
use AppBundle\Util\Now;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var $userManager UserManager
     */
    private $userManager;

    /**
     * @var $manager EntityManager
     */
    private $manager;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->manager = $this->container->get('doctrine')->getManager();
        $this->userManager = $this->container->get('fos_user.user_manager');
    }

    public function load(ObjectManager $manager)
    {
        $userUser1 = $this->createUser(new User(), 'fabsgc', 'fabien.beaujean@hotmail.fr', 'password');
        $userUser1->addRole('ROLE_USER');
        $this->userManager->updateUser($userUser1, true);

        $userUser2 = $this->createUser(new User(), 'qwerty', 'qwerty@hotmail.fr', 'password');
        $userUser2->addRole('ROLE_USER');
        $this->userManager->updateUser($userUser2, true);

        $userUser3 = $this->createUser(new User(), 'vinm', 'vinm@hotmail.fr', 'password');
        $userUser3->addRole('ROLE_USER');
        $this->userManager->updateUser($userUser3, true);

        $userAdmin = $this->createUser(new User(), 'admin', 'admin@agreed.fr', 'password');
        $userAdmin->addRole('ROLE_ADMIN');
        $this->userManager->updateUser($userAdmin, true);

        $survey = $this->createSurvey(new Survey(), $userUser1, 'CHOICE', 'Sondage 1', 'Description du sondage', true);
        $this->manager->persist($survey);
        $this->manager->flush($survey);

        $participant1 = $this->createParticipant(new Participant(), $survey, $userUser1);
        $this->manager->persist($participant1);
        $this->manager->flush($participant1);

        $participant2 = $this->createParticipant(new Participant(), $survey, $userUser2, true);
        $this->manager->persist($participant2);
        $this->manager->flush($participant2);

        $participant3 = $this->createParticipant(new Participant(), $survey, $userUser3);
        $this->manager->persist($participant3);
        $this->manager->flush($participant3);

        $choice1 = $this->createChoice(new Choice(), $survey, 'Choix 1', 1);
        $this->manager->persist($choice1);
        $this->manager->flush($choice1);

        $choice2 = $this->createChoice(new Choice(), $survey, 'Choix 2', 2);
        $this->manager->persist($choice2);
        $this->manager->flush($choice2);

        $vote1 = $this->createVote(new Vote(), $survey, $choice1, $participant1);
        $this->manager->persist($vote1);
        $this->manager->flush($vote1);
    }

    /**
     * @param User $user
     * @param string $username
     * @param string $email
     * @param string $password
     * @return User
     */
    private function createUser(User $user, $username = '', $email = '', $password = 'password') {
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $user->setRegistrationDate(Now::now());

        return $user;
    }

    /**
     * @param Survey $survey
     * @param User $user
     * @param string $type
     * @param string $name
     * @param string $description
     * @param bool $multiple
     * @return Survey
     */
    private function createSurvey(Survey $survey, User $user, $type = 'CHOICE', $name = '', $description = '', $multiple = false) {
        $survey->setName($name);
        $survey->setDescription($description);
        $survey->setType($type);
        $survey->setMultiple($multiple);
        $survey->setOpened(true);
        $survey->setUser($user);

        return $survey;
    }

    /**
     * @param Participant $participant
     * @param Survey $survey
     * @param User $user
     * @param bool $hasVoted
     * @return Participant
     */
    private function createParticipant(Participant $participant, Survey $survey, User $user, $hasVoted = false) {
        $tokenGenerator = $this->container->get('fos_user.util.token_generator');

        $participant->setSurvey($survey);
        $participant->setUser($user);
        $participant->setHasVoted($hasVoted);
        $participant->setToken($tokenGenerator->generateToken());

        return $participant;
    }

    /**
     * @param Choice $choice
     * @param Survey $survey
     * @param string $description
     * @return Choice
     */
    private function createChoice(Choice $choice, Survey $survey, $description = '') {
        $choice->setSurvey($survey);
        $choice->setDescription($description);

        return $choice;
    }

    /**
     * @param Vote $vote
     * @param Survey $survey
     * @param Choice $choice
     * @param Participant $participant
     * @return Vote
     */
    private function createVote(Vote $vote, Survey $survey, Choice $choice, Participant $participant) {
        $vote->setSurvey($survey);
        $vote->setChoice($choice);
        $vote->setParticipant($participant);

        return $vote;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder() {
        return 1;
    }
}