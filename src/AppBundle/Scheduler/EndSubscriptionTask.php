<?php
namespace AppBundle\Scheduler;

use AppBundle\Entity\Meeting;
use AppBundle\Entity\Subscription;
use AppBundle\Service\Mailer;
use AppBundle\Service\Subscriptions;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class EndSubscriptionTask extends SchedulerTask
{


    /**
     * Get the task description
     * @return string
     */
    public function getDescription()
    {
        return 'end subscription task';
    }

    /**
     * Execute the scheduler task
     * @return mixed
     */
    public function run()
    {

        /**
         * @var $doctrine Registry
         * @var $subService Subscriptions
         * @var $sub Subscription
         */
        $doctrine = $this->container->get('doctrine');
        $subService = $this->container->get('app.subscriptions');


        $subs = $doctrine->getRepository('AppBundle:Subscription')->getSubscriptionsToEnd();

        foreach ($subs as $sub)
        {
            $this->output->writeln('Processing subscription #'.$sub->getId());
            $subService->end($sub);
        }

        $doctrine->getManager()->flush();

    }



}