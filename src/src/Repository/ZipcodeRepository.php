<?php

namespace App\Repository;

use App\Entity\Zipcode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Zipcode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Zipcode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Zipcode[]    findAll()
 * @method Zipcode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZipcodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zipcode::class);
    }

    // /**
    //  * @return Zipcode[] Returns an array of Zipcode objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('z.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Zipcode
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
