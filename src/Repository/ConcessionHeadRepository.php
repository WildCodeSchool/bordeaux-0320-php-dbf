<?php

namespace App\Repository;

use App\Entity\ConcessionHead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConcessionHead|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConcessionHead|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConcessionHead[]    findAll()
 * @method ConcessionHead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcessionHeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConcessionHead::class);
    }

    // /**
    //  * @return ConcessionHead[] Returns an array of ConcessionHead objects
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
    public function findOneBySomeField($value): ?ConcessionHead
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
