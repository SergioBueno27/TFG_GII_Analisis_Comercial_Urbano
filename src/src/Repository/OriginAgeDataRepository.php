<?php

namespace App\Repository;

use App\Entity\OriginAgeData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OriginAgeData|null find($id, $lockMode = null, $lockVersion = null)
 * @method OriginAgeData|null findOneBy(array $criteria, array $orderBy = null)
 * @method OriginAgeData[]    findAll()
 * @method OriginAgeData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OriginAgeDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OriginAgeData::class);
    }

    // /**
    //  * @return OriginAgeData[] Returns an array of OriginAgeData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OriginAgeData
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
