<?php
namespace AppBundle\Service;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Offer;
use AppBundle\Entity\Professional;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\User;
use AppBundle\Util\Now;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Templating\EngineInterface;

class Subscriptions
{

    use ContainerAwareTrait;


    public function subscribe(Professional $professional, Offer $offer)
    {

        /**
         * @var $doctrine Registry
         */
        $doctrine = $this->container->get('doctrine');

        $subscriptionRepo = $doctrine->getRepository('AppBundle:Subscription');

        $subscription = new Subscription();
        $subscription
            ->setRecurrences($offer->getRecurrences())
            ->setOffer($offer)
            ->setProfessional($professional)
            ->setPaymentMethod($professional->getPaymentMethod()->getMethod())
            ->setStatus('WAITING_PAYMENT');


        $doctrine->getManager()->persist($subscription);

        return $subscription;
    }


    public function consume(Subscription $subscription)
    {
        /**
         * @var $doctrine Registry
         */
        $doctrine = $this->container->get('doctrine');

        $bill = new Bill();
        $bill
            ->setSubscription($subscription)
            ->setProfessional($subscription->getProfessional())
            ->setAmount($subscription->getOffer()->getPrice());

        $doctrine->getManager()->persist($bill);

        if(!$subscription->getNextPaymentDate()) $subscription->setNextPaymentDate(Now::now());

        $subscription->incrementNextPaymentDate();
        $doctrine->getManager()->persist($subscription);

        return $bill;
    }

    /**
     * On payment authorization received
     * @param Subscription $subscription
     */
    public function start(Subscription $subscription)
    {
        /**
         * @var $doctrine Registry
         */
        $doctrine = $this->container->get('doctrine');
        $current =$doctrine->getRepository('AppBundle:Subscription')->findCurrent($subscription->getProfessional());

        if($current)
        {
            // QUEUE
            $subscription->setNextPaymentDate($current->getEndDate());
            $subscription->computeEndDate();
            $subscription->setStatus('QUEUED');
        }
        else
        {
            // SET CURRENT
            $subscription->setNextPaymentDate(Now::now());
            $subscription->computeEndDate();
            $subscription->setStatus('CURRENT');
        }


        $doctrine->getManager()->persist($subscription);


    }

    /**
     * Called when CURRENT subscription end
     */
    public function end(Subscription $subscription)
    {
        /**
         * @var $doctrine Registry
         */

        $doctrine = $this->container->get('doctrine');

        $subscription->setStatus('ENDED');
        $doctrine->getManager()->persist($subscription);

        $next = $doctrine->getRepository('AppBundle:Subscription')->nextInQueue($subscription->getProfessional());

        if(!$next) return;

        $next->setStatus('CURRENT');
        $next->setNextPaymentDate($subscription->getNextPaymentDate());
        $next->computeEndDate();

        $doctrine->getManager()->persist($next);

        return;
    }

    /**
     * Suspend subscription
     * @param Subscription $subscription
     */
    public function suspend(Subscription $subscription)
    {
        if(!($subscription->getStatus() == 'CURRENT')) return;

        /**
         * @var $doctrine Registry
         */

        $doctrine = $this->container->get('doctrine');

        $subscription->setStatus('SUSPENDED');
        $subscription->setEndDate(null);

        $doctrine->getManager()->persist($subscription);

        return;
    }

    /**
     * Suspend subscription
     * @param Subscription $subscription
     */
    public function unsuspend(Subscription $subscription)
    {
        if(!($subscription->getStatus() == 'SUSPENDED')) return;

        /**
         * @var $doctrine Registry
         */

        $doctrine = $this->container->get('doctrine');

        $subscription->setStatus('CURRENT');

        $subscription->setNextPaymentDate(Now::now()->add(new \DateInterval($subscription->getOffer()->getInterval())));
        $subscription->computeEndDate();

        $doctrine->getManager()->persist($subscription);

        return;
    }



    /**
     * Find current subscription for a professional
     * @param Professional $professional
     * @return mixed
     */
    public function current(Professional $professional)
    {
        /**
         * @var $doctrine Registry
         */
        $doctrine = $this->container->get('doctrine');

        return $doctrine->getRepository('AppBundle:Subscription')->findCurrent($professional);
    }

    public function queued(Professional $professional)
    {
        /**
         * @var $doctrine Registry
         */
        $doctrine = $this->container->get('doctrine');

        return $doctrine->getRepository('AppBundle:Subscription')->findQueued($professional);
    }


}
