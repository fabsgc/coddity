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
use Doctrine\ORM\Mapping\ClassMetadata;
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
        // USERS

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

        // FIRST SURVEY

        $survey1 = $this->createSurvey(new Survey(), $userUser1, 'DATE', 'Barbecue de l\'été', 'Bonjour à tous,
        
Pour fêter l\'arrivée de l\'été, j\'organise pendant le mois de Juin un grand barbecue auquel vous êtes conviés. Pour pouvoir donner une date, je partage ce petit sondage pour être sûr que cela conviendra au plus grand nombre !', true);
        $survey1->setId(1);
        $this->manager->persist($survey1);
        $metadata = $this->manager->getClassMetaData(get_class($survey1));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $this->manager->flush($survey1);

        $participant11 = $this->createParticipant(new Participant(), $survey1, $userUser1, 'R6w2QXrqfkYk_jPTaDGo0FqUAZsaL8h3NsUyxZ65GFg');
        $this->manager->persist($participant11);
        $this->manager->flush($participant11);

        $participant12 = $this->createParticipant(new Participant(), $survey1, $userUser2, 'xFomde8U5QjzIvW5QzVzrcokfkP6ifbrnJv0IuyzKP8');
        $this->manager->persist($participant12);
        $this->manager->flush($participant12);

        $participant13 = $this->createParticipant(new Participant(), $survey1, $userUser3, 'IWRz0PTI24pTNo6IKXpRg3ML4I3t2F2voFsXyCCvYzk');
        $this->manager->persist($participant13);
        $this->manager->flush($participant13);

        $choice11 = $this->createChoice(new Choice(), $survey1, '3/06/2017');
        $this->manager->persist($choice11);
        $this->manager->flush($choice11);

        $choice12 = $this->createChoice(new Choice(), $survey1, '10/06/2017');
        $this->manager->persist($choice12);
        $this->manager->flush($choice12);

        $choice13 = $this->createChoice(new Choice(), $survey1, '17/06/2017');
        $this->manager->persist($choice13);
        $this->manager->flush($choice13);

        $choice14 = $this->createChoice(new Choice(), $survey1, '24/06/2017');
        $this->manager->persist($choice14);
        $this->manager->flush($choice14);

        // SECOND SURVEY

        $survey2 = $this->createSurvey(new Survey(), $userUser1, 'CHOICE', 'Camping cet été', 'Salut à tous,
        
Cet été, on a décidé de partir camper en Vendée. Par contre, pas facile de choisir un camping. Et donc, j\'ai décidé de lancer ce sondage pour pouvoir savoir lequel est votre préféré. Allez, votez vite !', false);
        $survey2->setId(2);
        $this->manager->persist($survey2);
        $metadata = $this->manager->getClassMetaData(get_class($survey2));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $this->manager->flush($survey2);

        $participant21 = $this->createParticipant(new Participant(), $survey2, $userUser1, 'R6w2QXrqfkYk_jPTaDGo0FqUAZsaL8h3NsUyxZ65GFg');
        $this->manager->persist($participant21);
        $this->manager->flush($participant21);

        $participant22 = $this->createParticipant(new Participant(), $survey2, $userUser2, 'xFomde8U5QjzIvW5QzVzrcokfkP6ifbrnJv0IuyzKP8');
        $this->manager->persist($participant22);
        $this->manager->flush($participant22);

        $participant23 = $this->createParticipant(new Participant(), $survey2, $userUser3, 'IWRz0PTI24pTNo6IKXpRg3ML4I3t2F2voFsXyCCvYzk');
        $this->manager->persist($participant23);
        $this->manager->flush($participant23);

        $choice21 = $this->createChoice(new Choice(), $survey2, 'Camping de la Dune');
        $this->manager->persist($choice21);
        $this->manager->flush($choice21);

        $choice22 = $this->createChoice(new Choice(), $survey2, 'Camping Les Flots Bleus');
        $this->manager->persist($choice22);
        $this->manager->flush($choice22);

        $choice23 = $this->createChoice(new Choice(), $survey2, 'Camping La Pomme de Pin');
        $this->manager->persist($choice23);
        $this->manager->flush($choice23);

        $choice24 = $this->createChoice(new Choice(), $survey2, 'Camping Les Pirons');
        $this->manager->persist($choice24);
        $this->manager->flush($choice24);
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
     * @param string $token
     * @return Participant
     */
    private function createParticipant(Participant $participant, Survey $survey, User $user, $token = null) {
        $tokenGenerator = $this->container->get('fos_user.util.token_generator');

        if($token == null) {
            $token = $tokenGenerator->generateToken();
        }

        $participant->setSurvey($survey);
        $participant->setUser($user);
        $participant->setEmail($user->getEmail());
        $participant->setHasVoted(false);
        $participant->setToken($token);

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
