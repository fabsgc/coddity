<?php

namespace UserBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
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

    }
}