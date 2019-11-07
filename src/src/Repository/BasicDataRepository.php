<?php

namespace App\Repository;

use App\Entity\BasicData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BasicData|null find($id, $lockMode = null, $lockVersion = null)
 * @method BasicData|null findOneBy(array $criteria, array $orderBy = null)
 * @method BasicData[]    findAll()
 * @method BasicData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasicDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BasicData::class);
    }

    // /**
    //  * @return BasicData[] Returns an array of BasicData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BasicData
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
