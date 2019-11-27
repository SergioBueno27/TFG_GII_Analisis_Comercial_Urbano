<?php

namespace App\Repository;

use App\Entity\DayData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DayData|null find($id, $lockMode = null, $lockVersion = null)
 * @method DayData|null findOneBy(array $criteria, array $orderBy = null)
 * @method DayData[]    findAll()
 * @method DayData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DayData::class);
    }

    // /**
    //  * @return DayData[] Returns an array of DayData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DayData
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
