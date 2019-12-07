<?php

namespace App\Repository;

use App\Entity\OriginGenderData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OriginGenderData|null find($id, $lockMode = null, $lockVersion = null)
 * @method OriginGenderData|null findOneBy(array $criteria, array $orderBy = null)
 * @method OriginGenderData[]    findAll()
 * @method OriginGenderData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OriginGenderDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OriginGenderData::class);
    }

    // /**
    //  * @return OriginGenderData[] Returns an array of OriginGenderData objects
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
    public function findOneBySomeField($value): ?OriginGenderData
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
