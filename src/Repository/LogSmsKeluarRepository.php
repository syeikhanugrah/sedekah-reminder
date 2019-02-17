<?php

namespace App\Repository;

use App\Entity\LogSmsKeluar;
use App\Entity\Pengingat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LogSmsKeluarRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogSmsKeluar::class);
    }

    public function findByLogPengingatSekarang(Pengingat $pengingat)
    {
        $tanggalSekarang = new \DateTime();

        return $this->createQueryBuilder('logSmsKeluar')
            ->where('logSmsKeluar.tanggal = :tanggalSekarang')
            ->andWhere('logSmsKeluar.pengingat = :pengingat')
            ->setParameter('tanggalSekarang', $tanggalSekarang->format('Y-m-d'))
            ->setParameter('pengingat', $pengingat)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
