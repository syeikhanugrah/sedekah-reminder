<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByConfirmationToken($confirmationToken)
    {
        return $this->createQueryBuilder('u')
            ->where('u.confirmationToken = :confirmationToken')
            ->setParameter('confirmationToken', $confirmationToken)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUsernameOrEmail($identitas)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email OR u.username = :username')
            ->setParameter('email', $identitas)
            ->setParameter('username', $identitas)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllExcept($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id != :userId')
            ->setParameter('userId', $id)
            ->getQuery()
            ->getResult();
    }

    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email OR u.username = :username')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
