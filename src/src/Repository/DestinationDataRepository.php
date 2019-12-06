<?php

namespace App\Repository;

use App\Entity\DestinationData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DestinationData|null find($id, $lockMode = null, $lockVersion = null)
 * @method DestinationData|null findOneBy(array $criteria, array $orderBy = null)
 * @method DestinationData[]    findAll()
 * @method DestinationData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DestinationDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DestinationData::class);
    }

    // /**
    //  * @return DestinationData[] Returns an array of DestinationData objects
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
    public function findOneBySomeField($value): ?DestinationData
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
