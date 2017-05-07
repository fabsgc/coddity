<?php
namespace AppBundle\Scheduler;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Meeting;
use AppBundle\Entity\Subscription;
use AppBundle\Service\Mailer;
use AppBundle\Service\Subscriptions;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class SuspendSubscriptionTask extends SchedulerTask
{


    /**
     * Get the task description
     * @return string
     */
    public function getDescription()
    {
        return 'suspend subscription task';
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
         * @var $bill Bill
         */
        $doctrine = $this->container->get('doctrine');
        $subService = $this->container->get('app.subscriptions');


        $bills = $doctrine->getRepository('AppBundle:Subscription')->getSubscriptionsToSuspend();

        foreach ($bills as $bill)
        {
            $this->output->writeln('Processing subscription #'.$bill->getSubscription()->getId());
            $subService->suspend($bill->getSubscription());
        }

        $doctrine->getManager()->flush();

    }



}