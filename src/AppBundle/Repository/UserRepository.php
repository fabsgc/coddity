<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     * Find all users by type (user|admin)
     * @param string $type
     * @return array
     */
    public function findByType($type)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u INSTANCE OF :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find user by email
     * @param string $email
     * @return array
     */
    public function findByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }
}
