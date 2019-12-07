<?php

namespace App\Repository;

use App\Entity\OriginData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OriginData|null find($id, $lockMode = null, $lockVersion = null)
 * @method OriginData|null findOneBy(array $criteria, array $orderBy = null)
 * @method OriginData[]    findAll()
 * @method OriginData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OriginDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OriginData::class);
    }

    // /**
    //  * @return OriginData[] Returns an array of OriginData objects
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
    public function findOneBySomeField($value): ?OriginData
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
