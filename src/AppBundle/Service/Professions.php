<?php
namespace AppBundle\Service;

use AppBundle\Repository\ProfessionalRepository;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class Professions
{

    use ContainerAwareTrait;

    private $defaultProfessions;

    /**
     * Professions constructor.
     * @param $defaultProfessions
     */
    public function __construct($defaultProfessions)
    {
        $this->defaultProfessions = json_decode($defaultProfessions, true);
    }

    public function getAll()
    {
        /**
         * @var $repo ProfessionalRepository
         */
        $repo = $this->container->get('doctrine')->getRepository('AppBundle:Professional');

        $professions = $repo->getProfessions();
        $specializations = array();


        foreach ($professions as $profession) {
            $specializations[$profession] = $repo->getSpecializationsForProfession($profession);
        }

        $total = array_merge_recursive($this->defaultProfessions, $specializations);

        foreach ($total as $profession => $specializations) {
            $total[$profession] = array_unique($specializations);
        }

        return $total;
    }

    public function getDefault()
    {

        $total = $this->defaultProfessions;

        foreach ($total as $profession => $specializations) {
            $total[$profession] = array_unique($specializations);
        }

        return $total;
    }

    public function getProfessionsOnly()
    {

        /**
         * @var $repo ProfessionalRepository
         */
        $repo = $this->container->get('doctrine')->getRepository('AppBundle:Professional');

        $professions = $repo->getProfessions();

        return array_filter(array_unique(array_merge($professions, array_keys($this->defaultProfessions))));
    }

    public function getSpecializationsOnly()
    {

        /**
         * @var $repo ProfessionalRepository
         */
        $repo = $this->container->get('doctrine')->getRepository('AppBundle:Professional');

        $total = $repo->getSpecializations();

        foreach ($this->defaultProfessions as $dspes)
        {
            $total = array_filter(array_unique(array_merge($total, $dspes)));
        }

        return $total ;
    }


}
