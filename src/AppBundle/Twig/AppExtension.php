<?php

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManager;

class AppExtension extends \Twig_Extension
{
    /** @var EntityManager  */
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('date_fr', array($this, 'dateFr')),
            new \Twig_SimpleFilter('date_fr_no_hours', array($this, 'dateFrNoHours')),
            new \Twig_SimpleFilter('date_fr_short', array($this, 'dateFrShort')),
            new \Twig_SimpleFilter('hasSubscription', array($this, 'hasSubscriptionFilter')),
            new \Twig_SimpleFilter('offer_duration', array($this, 'offerDuration'))
        );
    }

    public function dateFr(\DateTime $date = null, $format = 'EEEE d MMMM yyyy à HH:mm')
    {
        if(!$date) return 'Jamais';

        $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
        $formatter->setPattern($format);

        $date = $formatter->format($date);
        return $date;
    }

    public function dateFrNoHours(\DateTime $date = null)
    {
        return $this->dateFr($date, 'EEEE d MMMM yyyy');
    }

    public function dateFrShort(\DateTime $date = null)
    {
        return $this->dateFr($date, 'dd/MM/yyyy HH:mm');
    }

    public function getName()
    {
        return 'app_extension';
    }
}