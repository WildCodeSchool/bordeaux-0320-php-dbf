<?php

namespace App\Repository;

use App\Entity\CallTransfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallTransfer|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallTransfer|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallTransfer[]    findAll()
 * @method CallTransfer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallTransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallTransfer::class);
    }

    // /**
    //  * @return CallTransfer[] Returns an array of CallTransfer objects
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
    public function findOneBySomeField($value): ?CallTransfer
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
