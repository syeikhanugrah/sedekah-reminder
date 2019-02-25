<?php

namespace App\Repository;

use App\Entity\Pengingat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PengingatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pengingat::class);
    }

    public function findAllEntities(array $options)
    {
        $qb = $this->createQueryBuilder('pengingat')
            ->orderBy('pengingat.tanggalAwal')
        ;

        if (!$options['isAdmin']) {
            $qb->where('pengingat.user = :user')->setParameter('user', $options['user']);
        }

        return $qb->getQuery()->getResult();
    }
}
