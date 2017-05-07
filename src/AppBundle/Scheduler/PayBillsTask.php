<?php
namespace AppBundle\Scheduler;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Meeting;
use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\Subscription;
use AppBundle\Service\Mailer;
use AppBundle\Service\Subscriptions;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class PayBillsTask extends SchedulerTask
{


    /**
     * Get the task description
     * @return string
     */
    public function getDescription()
    {
        return 'pay bills task';
    }

    /**
     * Execute the scheduler task
     * @return mixed
     */
    public function run()
    {

        /**
         * @var $doctrine Registry
         * @var $bill Bill
         */
        $doctrine = $this->container->get('doctrine');

        $bills = $doctrine->getRepository('AppBundle:Bill')->getBillsToPay();


        foreach ($bills as $bill)
        {
            $this->output->writeln('Processing bill #'.$bill->getId());

            switch ($bill->getSubscription()->getPaymentMethod())
            {
                case PaymentMethod::MONETICO:
                    $this->container->get('app.monetico')->charge($bill);
                    break;
                case PaymentMethod::DIRECT_DEBIT:
                    // TODO
                    break;
            }
        }


    }



}