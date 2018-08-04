<?php

namespace AppBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends \Doctrine\ORM\EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        try {
            return $this
                ->createQueryBuilder('user')
                ->where('user.username = :username OR user.email = :email')
                ->setParameter('username', $username)
                ->setParameter('email', $username)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }
}