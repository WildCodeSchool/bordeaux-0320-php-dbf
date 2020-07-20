<?php

namespace App\Repository;

use App\Entity\Right;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Right|null find($id, $lockMode = null, $lockVersion = null)
 * @method Right|null findOneBy(array $criteria, array $orderBy = null)
 * @method Right[]    findAll()
 * @method Right[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Right::class);
    }

    // /**
    //  * @return Right[] Returns an array of Right objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Right
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
