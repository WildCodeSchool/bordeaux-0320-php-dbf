<?php

namespace App\Repository;

use App\Entity\Concession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Concession|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concession|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concession[]    findAll()
 * @method Concession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concession::class);
    }

    // /**
    //  * @return Concession[] Returns an array of Concession objects
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
    public function findOneBySomeField($value): ?Concession
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
