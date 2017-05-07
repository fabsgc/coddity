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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ConsumeSubscriptionTask extends SchedulerTask
{


    /**
     * Get the task description
     * @return string
     */
    public function getDescription()
    {
        return 'consume subscription task';
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
         * @var $bill Bill
         * @var $mailer Mailer
         */
        $doctrine = $this->container->get('doctrine');
        $subService = $this->container->get('app.subscriptions');
        $mailer = $this->container->get('app.mailer');


        $subs = $doctrine->getRepository('AppBundle:Subscription')->getSubscriptionsToConsume();

        foreach ($subs as $sub)
        {
            $this->output->writeln('Processing subscription #'.$sub->getId());
            $bill = $subService->consume($sub);

            $doctrine->getManager()->flush();

            $billUrl = $this->container->get('router')->generate('bill_pdf', array(
                'id' => $bill->getId(),
                'token' => $sub->getProfessional()->getToken(),
            ), UrlGeneratorInterface::ABSOLUTE_URL);


            // Bill mail
            $mailer->send($bill->getProfessional(), 'Facture N°'.$bill->getId().' du '.$bill->getDate()->format('d/m/Y'), 'Bill/bill', array(
                'bill' => $bill,
                'billUrl' => $billUrl
            ));


        }

        $doctrine->getManager()->flush();

    }



}