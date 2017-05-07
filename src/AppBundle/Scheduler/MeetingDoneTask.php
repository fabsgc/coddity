<?php
namespace AppBundle\Scheduler;

use AppBundle\Entity\Meeting;
use AppBundle\Service\Mailer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class MeetingDoneTask extends SchedulerTask
{


    /**
     * Get the task description
     * @return string
     */
    public function getDescription()
    {
        return 'meeting done task';
    }

    /**
     * Execute the scheduler task
     * @return mixed
     */
    public function run()
    {
        /**
         * @var $doctrine Registry
         * @var $mailer Mailer
         * @var $meeting Meeting
         */
        $doctrine = $this->container->get('doctrine');

        $mailer = $this->container->get('app.mailer');

        $doneMeetings = $doctrine->getRepository('AppBundle:Meeting')->getSchedulerDoneMeetings();

        foreach ($doneMeetings as $meeting)
        {
            $this->output->writeln('Processing meeting #'.$meeting->getId());

            $meeting->setStatus('done');
            $doctrine->getManager()->persist($meeting);
            $doctrine->getManager()->flush();

            $markUrl = $this->container->get('router')->generate('meeting_mark_professional', ['id' => $meeting->getId(), 'token' => $meeting->getCustomer()->getToken()], true);

            // Customer mail
            $mailer->send($meeting->getCustomer(), 'Parce que votre avis compte', 'Meeting/done_customer', array(
                'meeting' => $meeting,
                'meetingMarkUrl' => $markUrl
            ));

        }
    }
}