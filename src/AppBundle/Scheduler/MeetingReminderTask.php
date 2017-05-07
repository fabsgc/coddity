<?php
namespace AppBundle\Scheduler;

use AppBundle\Entity\Meeting;
use AppBundle\Service\Mailer;
use AppBundle\Service\Sms;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class MeetingReminderTask extends SchedulerTask
{


    /**
     * Get the task description
     * @return string
     */
    public function getDescription()
    {
        return 'meeting reminder task';
    }

    /**
     * Execute the scheduler task
     * @return mixed
     */
    public function run()
    {
        $this->sendSmsReminder(1);
        $this->sendEmailReminder(5);
    }

    public function sendEmailReminder($days)
    {

        $interval = new \DateInterval('P'.$days.'D');

        /**
         * @var $doctrine Registry
         * @var $mailer Mailer
         * @var $meeting Meeting
         */
        $doctrine = $this->container->get('doctrine');

        $mailer = $this->container->get('app.mailer');

        $max = clone($this->currentExecution);
        $min = clone($this->lastExecution);
        $min->add($interval);
        $max->add($interval);

        $meetings = $doctrine->getRepository('AppBundle:Meeting')->getMeetingsBetween($min, $max);

        foreach ($meetings as $meeting)
        {

            $this->output->writeln('Processing meeting #'.$meeting->getId());

            // Customer mail
            $mailer->send($meeting->getCustomer(), 'Rappel de votre rendez-vous du '.$meeting->getStart()->format('d/m/Y'), 'Meeting/reminder_customer', array(
                'meeting' => $meeting,
                'days' => $days,
            ));

        }
    }

    public function sendSmsReminder($days)
    {
        $interval = new \DateInterval('P'.$days.'D');

        /**
         * @var $doctrine Registry
         * @var $sms Sms
         * @var $meeting Meeting
         */
        $doctrine = $this->container->get('doctrine');

        $sms = $this->container->get('app.sms');

        $max = clone($this->currentExecution);
        $min = clone($this->lastExecution);
        $min->add($interval);
        $max->add($interval);

        $meetings = $doctrine->getRepository('AppBundle:Meeting')->getMeetingsBetween($min, $max);

        foreach ($meetings as $meeting)
        {

            $this->output->writeln('Processing meeting #'.$meeting->getId());

            // SMS MESSAGE

            $message = sprintf('
            Bonjour « ... »\n\n

            ProfessionLib vous rappelle votre rendez-vous avec : \n\n
            
            %s %s\n
            Demain (%s) à %s\n
            Au %s\n
            
            \n\n
            Nous vous rappelons que pour toutes modifications, il vous suffit de vous connecter à votre espace personnel.\n\n
            
            Merci de votre confiance.
            ',
                $meeting->getProfessional()->getTitle(),
                strtoupper($meeting->getProfessional()->getLastname()),
                $meeting->getStart()->format('d/m/Y'),
                $meeting->getStart()->format('H\hi'),
                $meeting->getProfessional()->getLocation()->getFullAddress()
            );

            
            $sms->send($meeting->getCustomer(),$message);

        }
    }

}