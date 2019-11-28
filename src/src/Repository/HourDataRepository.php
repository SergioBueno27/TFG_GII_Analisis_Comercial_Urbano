<?php

namespace App\Repository;

use App\Entity\HourData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HourData|null find($id, $lockMode = null, $lockVersion = null)
 * @method HourData|null findOneBy(array $criteria, array $orderBy = null)
 * @method HourData[]    findAll()
 * @method HourData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HourDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HourData::class);
    }

    // /**
    //  * @return HourData[] Returns an array of HourData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HourData
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
