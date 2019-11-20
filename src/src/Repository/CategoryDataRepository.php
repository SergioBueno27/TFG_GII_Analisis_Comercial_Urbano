<?php

namespace App\Repository;

use App\Entity\CategoryData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategoryData|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryData|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryData[]    findAll()
 * @method CategoryData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryData::class);
    }

    // /**
    //  * @return CategoryData[] Returns an array of CategoryData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryData
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
