<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Survey;

/**
 * SubscriptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResultRepository extends \Doctrine\ORM\EntityRepository
{
    public function findBySurvey(Survey $survey) {
        return $this->createQueryBuilder('r')
            ->where('r.survey = :s')
            ->setParameter('s', $survey)
            ->getQuery()->getResult();
    }
}