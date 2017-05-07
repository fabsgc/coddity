<?php

namespace UserBundle\DataFixtures\ORM;

use AppBundle\Entity\Availability;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Location;
use AppBundle\Entity\Mark;
use AppBundle\Entity\Meeting;
use AppBundle\Entity\Offer;
use AppBundle\Entity\Profession;
use AppBundle\Entity\Professional;
use AppBundle\Entity\ProfessionalSettings;
use AppBundle\Entity\Question;
use AppBundle\Entity\Specialization;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\User;
use AppBundle\Service\Subscriptions;
use AppBundle\Util\Now;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Faker\Factory;
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

    /**
     * @var $faker Factory;
     */
    private $faker;


    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->manager = $this->container->get('doctrine')->getManager();
        $this->userManager = $this->container->get('fos_user.user_manager');
        $this->faker = Factory::create('fr_FR');

    }



    public function load(ObjectManager $manager)
    {
        // COUNTS

        $internCountFactor = 1;
        $professionalCount = 5;
        $customerCount = 5;

        // ENTITIES
        $users = array();
        $internUsers = array();
        $accountManagers = array();
        $professionalUsers = array();
        $customerUsers = array();
        $availabilities = array();
        $meetings = array();
        $marks = array();
        $offers = array();
        $subscriptions = array();
        $questions = array();

        // FILLING

        // INTERN USERS

        $roles = [
            'ROLE_SUPER_ADMIN',
            'ROLE_SALES_MANAGER',
            'ROLE_FINANCIAL_OFFICER',
            'ROLE_ANALYST',
            'ROLE_TECHNICAL_ADMIN',
            'ROLE_PLATFORM_ADMIN',
            'ROLE_ACCOUNT_MANAGER',
            'ROLE_ACCOUNT_MANAGER',
            'ROLE_ACCOUNT_MANAGER',
            'ROLE_ACCOUNT_MANAGER',
            'ROLE_ACCOUNT_MANAGER',
            'ROLE_OFFER_MANAGER',
        ];

        $n = count($roles) * $internCountFactor;

        for($i = 0; $i<$n; $i++)
        {

            if($i == 0)
            {
                $user = $this->genUser(new User(), 'admin', 'admin', 'admin@gmail.com');
            }
            else $user = $this->genUser(new User());

            dump('CREATING INTERN USER ('.($i+1).'/'.$n.') '. $user->getEmail());

            $user->addRole($roles[$i%count($roles)]);


            $this->userManager->updateUser($user, false);

            if($user->hasRole('ROLE_ACCOUNT_MANAGER')) $accountManagers[] = $user;
            $users[] = $user;
            $internUsers[] = $user;
        }


        $professions = [
            'Notaire' => [
                'Retraites',
                'Divorces',
                'Successions'
            ],
            'Avocat' => [
                'Penal',
                'Prud\'hommes',
                'Commmerce',
            ]
        ];



        // PROFESSIONAL USERS

        $n = $professionalCount;

        for($i = 0; $i<$n; $i++)
        {
            /**
             * @var $professional Professional
             */
            $professional = $this->genUser(new Professional());

            dump('CREATING PROFESSIONAL USER ('.($i+1).'/'.$n.') '. $professional->getEmail());

            $honoraryFrom = $this->faker->numberBetween(10,30)*10;
            $honoraryTo = $honoraryFrom + $this->faker->numberBetween(10,30)*10;
            $profession = $this->faker->randomElement(array_keys($professions));
            $specialization = $this->faker->randomElement($professions[$profession]);

            $professional->addRole('ROLE_PROFESSIONAL')
                ->setCompany($this->faker->company)
                ->setEducations($this->faker->randomElements(
                    [
                        'BAC L',
                        'FAC de droit',
                        'Université de Strastbourg',
                        'Université de Paris',
                        'Université de Lyon',
                        'Université de Nantes',
                        'Université de Lille'
                    ],
                    $this->faker->numberBetween(0,2)
                ))
                ->setProfession($profession)
                ->setSpecialization($specialization)
                ->setHonoraryFrom($honoraryFrom)
                ->setHonoraryTo($honoraryTo)
                ->setAccessIndication($this->faker->sentence)
                ->setLanguage('Français')
                ->setHonoraryType('HOR')
                ->setSettings($this->genProfessionalSettings())
                ->setAccountManager($this->faker->randomElement($accountManagers))
                ->setPicture('default-picture.png')
            ;

            $this->userManager->updateUser($professional, false);
            $users[] = $professional;
            $professionalUsers[] = $professional;
        }

        // CUSTOMER USERS


        $n = $customerCount;

        for($i = 0; $i<$n; $i++)
        {
            /**
             * @var $customer Customer
             */
            $customer = $this->genUser(new Customer());

            dump('CREATING CUSTOMER USER ('.($i+1).'/'.$n.') '. $customer->getEmail());

            $customer->addRole('ROLE_CUSTOMER');

            $this->userManager->updateUser($customer, false);
            $users[] = $customer;
            $customerUsers[] = $customer;
        }

        // AVAILABILITIES

        foreach($professionalUsers as $professional)
        {
            if(!$professional->isEnabled()) continue;

            dump('CREATING AVAILABILITIES FOR '.$professional->getUsername());

            foreach (['mon', 'tue', 'wed', 'thu', 'fri'] as $day)
            {
                $start = new \DateTime('first '.$day.' of September 2016');
                $end = clone($start);
                $start->setTime($this->faker->numberBetween(8,11), $this->faker->randomElement([0,15,30,45]));
                $end->setTime($this->faker->numberBetween(15,20), $this->faker->randomElement([0,15,30,45]));

                $availability = new Availability();
                $availability
                    ->setProfessional($professional)
                    ->setAvailable(true)
                    ->setRecursionRuleEnd(new \DateTime('September 2017'))
                    ->setRecursionRule('P1W')
                    ->setStart($start)
                    ->setEnd($end);

                $availabilities[] = $availability;
                $this->manager->persist($availability);
            }

        }


        // USER FLUSH
        $this->manager->flush();

        // MEETINGS

        /**
         * @var $availability Availability
         * @var $start \DateTime
         */
        foreach($availabilities as $availability)
        {
            dump('CREATING MEETING FOR '.$availability->getProfessional()->getUsername());

            $int = new \DateInterval('PT'.$availability->getProfessional()->getSettings()->getMeetingDuration().'M');
            $recursion = new \DateInterval($availability->getRecursionRule());


            for($i = 0; $i<52; $i++)
            {

                if($this->faker->boolean(50)) continue;

                $endLimit = clone($availability->getEnd());
                $endLimit->sub($int);

                $start = $this->faker->dateTimeBetween($availability->getStart(),  $endLimit);
                $h = $start->format('H');
                $m = $start->format('i');
                $start->setTime($h, floor($m/15)*15);
                $this->addMultiple($start, $recursion, $i);

                $end = clone($start);
                $end->add($int);

                if($end < Now::now()) $status = 'done';
                else $status = 'confirmed';

                $meeting = new Meeting();
                $meeting
                    ->setProfessional($availability->getProfessional())
                    ->setCustomer($this->faker->randomElement($customerUsers))
                    ->setStatus($status)
                    ->setStart($start)
                    ->setEnd($end);

                $meetings[] = $meeting;
                $this->manager->persist($meeting);
            }
        }

        // MARKS
        /**
         * @var $meeting Meeting
         */


        foreach ($meetings as $meeting)
        {
            $now = Now::now();
            if($meeting->getEnd() < $now->add(new \DateInterval('P'.$this->faker->numberBetween(0,7).'D'))) continue;

            dump('CREATING MARK FOR '.$meeting->getProfessional()->getEmail());


            $mark = new Mark();
            $mark
                ->setMeeting($meeting)
                ->setCriteria1($this->faker->numberBetween(0,5))
                ->setCriteria2($this->faker->numberBetween(0,5))
                ->setCriteria3($this->faker->numberBetween(0,5))
                ->setAverage($this->faker->numberBetween(0,7))
                ->computeAverage();

            $marks[] = $mark;
            $this->manager->persist($mark);
        }

        // OFFERS

        $offersData = [
            [
                'price' => 99,
                'title' => '1 mois',
                'available' => true,
                'interval' => 'P1M',
                'recurrences' => 1,
            ],
            [
                'price' => 94,
                'title' => '6 mois',
                'available' => true,
                'interval' => 'P1M',
                'recurrences' => 6,
            ],
            [
                'price' => 90,
                'title' => '1 an',
                'available' => true,
                'interval' => 'P1M',
                'recurrences' => 12,
            ],
            [
                'price' => 0,
                'title' => '1 mois',
                'available' => true,
                'interval' => 'P1M',
                'recurrences' => 1,
            ],
        ];

        foreach ($offersData as $offer) {

            dump('CREATING OFFER '.$offer['title']);

            $entry = new Offer();
            $entry
                ->setPrice($offer['price'])
                ->setTitle($offer['title'])
                ->setAvailable($offer['available'])
                ->setRecurrences($offer['recurrences'])
                ->setInterval($offer['interval']);

            $manager->persist($entry);
            $offers[] = $entry;

        }

        // SUBSCRIPTIONS

        /**
         * @var $subscriptionsService Subscriptions
         */
        $subscriptionsService = $this->container->get('app.subscriptions');

        foreach ($professionalUsers as $professional){

            dump('CREATING SUBSCRIPTION FOR '.$professional->getEmail());

            $subscription = $subscriptionsService->subscribe($professional, $this->faker->randomElement($offers));
            $subscriptionsService->start($subscription);

            $this->manager->persist($subscription);

            $subscriptions[] = $subscription;
        }

        // FLUSH
        $this->manager->flush();

        // QUEUED SUBSCRIPTION

        foreach ($professionalUsers as $professional){

            dump('CREATING SUBSCRIPTION FOR '.$professional->getEmail());

            $subscription = $subscriptionsService->subscribe($professional, $this->faker->randomElement($offers));
            $subscriptionsService->start($subscription);

            $this->manager->persist($subscription);

            $subscriptions[] = $subscription;
        }



        // QUESTIONS

       $n = 15;

       for($i = 0 ; $i < $n; $i++)
       {
           dump('CREATING QUESTION');

           $entry = new Question();
           $entry
               ->setTitle($this->faker->sentence)
               ->setContent($this->faker->paragraph(10, true))
           ;

           $questions[] = $entry;
           $this->manager->persist($entry);
        }


        // FLUSH
        $this->manager->flush();


    }

    /**
     * @return Location
     */
    public function genLocation()
    {
        $location = new Location();

        $location
            ->setCity($this->faker->city)
            ->setCountry('France')
            ->setStreet($this->faker->streetAddress)
            ->setPostalCode($this->faker->postcode)
            ->setLongitude($this->faker->longitude())
            ->setLatitude($this->faker->latitude());

        return $location;
    }

    /**
     * @return User
     */
    public function genUser(User $user, $firstname = null, $lastname = null, $mail = null)
    {

        if(!$firstname) $firstname = $this->faker->firstName;
        if(!$lastname) $lastname = $this->faker->lastName;

        if(!$mail) $mail  = strtolower($this->remove_accents($firstname)).'.'.strtolower($this->remove_accents($lastname)).'@'.$this->faker->freeEmailDomain;

        $user
            ->setBirthDate($this->faker->dateTimeBetween('-80 years','-20 years'))
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($mail)
            ->setLocation($this->genLocation())
            ->setPhoneNumber('06'.$this->faker->randomNumber(8))
            ->setTitle($this->faker->randomElement(['M', 'MME', 'Maitre']))
            ->setPlainPassword('password')
            ->setRegistrationDate($this->faker->dateTimeBetween('-30 days', 'now'))
            ->setPicture('default-picture.png')
            ->setEnabled($this->faker->boolean(90));

        return $user;

    }

    /**
     * @return ProfessionalSettings
     */
    public function genProfessionalSettings()
    {
        $settings = new ProfessionalSettings();
        $mtlMin = $this->faker->numberBetween(1, 7)*24;
        $mtlMax = $mtlMin + $this->faker->numberBetween(30, 30*6)*24;
        $settings
            ->setMeetingDuration($this->faker->numberBetween(2,4)*30)
            ->setMeetingTimeLimitForCancellation($this->faker->numberBetween(1,72)*30)
            ->setMeetingTimeLimitMax($mtlMax)
            ->setMeetingTimeLimitMin($mtlMin)
            ->setPaperBill($this->faker->boolean(10))
            ->setTimeAfterMeeting($this->faker->numberBetween(1,6)*10);

        return $settings;
    }

    public function addMultiple(\DateTime &$date, \DateInterval $dateInterval, $amount)
    {
        for($i = 0; $i < $amount; $i++)
        {
            $date->add($dateInterval);
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }

    public function remove_accents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }
}