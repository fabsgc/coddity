<?php
namespace AppBundle\Scheduler;

use AppBundle\Entity\Meeting;
use AppBundle\Entity\ProspectReminder;
use AppBundle\Entity\Subscription;
use AppBundle\Service\Mailer;
use AppBundle\Service\Sms;
use AppBundle\Util\Now;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class SubscriptionReminderTask extends SchedulerTask
{


    /**
     * Get the task description
     * @return string
     */
    public function getDescription()
    {
        return 'subscription reminder task';
    }

    /**
     * Execute the scheduler task
     * @return mixed
     */
    public function run()
    {
        $this->sendEmailReminder(3);
        $this->sendEmailReminder(7);
        $this->accountManagerReminder(3);
    }

    public function accountManagerReminder($days)
    {
        $interval = new \DateInterval('P'.$days.'D');

        /**
         * @var $doctrine Registry
         * @var $mailer Mailer
         * @var $subscription Subscription
         */
        $doctrine = $this->container->get('doctrine');

        $max = clone($this->currentExecution);
        $min = clone($this->lastExecution);
        $min->add($interval);
        $max->add($interval);

        $subscriptions = $doctrine->getRepository('AppBundle:Subscription')->getEndingSubscriptionsBetween($min, $max);

        foreach ($subscriptions as $subscription)
        {
            $am = $subscription->getProfessional()->getAccountManager();
            if(!$am) continue;

            $prospectReminder = new ProspectReminder();
            $prospectReminder
                ->setProfessional($subscription->getProfessional())
                ->setAccountManager($am)
                ->setComment('Fin d\'abonnement ('.$subscription->getOffer()->getTitle().') du professionnel dans '.$days.' jours.')
                ->setRecallAlert(Now::now())
                ->setType('END_SUBSCRIPTION');

            $doctrine->getManager()->persist($prospectReminder);
        }

        $doctrine->getManager()->flush();
    }

    public function sendEmailReminder($days)
    {

        $interval = new \DateInterval('P'.$days.'D');

        /**
         * @var $doctrine Registry
         * @var $mailer Mailer
         * @var $subscription Subscription
         */
        $doctrine = $this->container->get('doctrine');

        $mailer = $this->container->get('app.mailer');

        $max = clone($this->currentExecution);
        $min = clone($this->lastExecution);
        $min->add($interval);
        $max->add($interval);

        $subscriptions = $doctrine->getRepository('AppBundle:Subscription')->getEndingSubscriptionsBetween($min, $max);

        foreach ($subscriptions as $subscription)
        {

            $this->output->writeln('Processing subscription #'.$subscription->getId());

            $mailer->send($subscription->getProfessional(), 'Fin de votre abonnement prochainement', 'Subscription/reminder', array(
                'user' => $subscription->getProfessional(),
                'subscription' => $subscription,
                'days' => $days,
            ));

        }
    }


}