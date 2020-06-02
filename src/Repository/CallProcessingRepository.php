<?php

namespace App\Repository;

use App\Entity\CallProcessing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallProcessing|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallProcessing|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallProcessing[]    findAll()
 * @method CallProcessing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallProcessingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallProcessing::class);
    }

    // /**
    //  * @return CallProcessing[] Returns an array of CallProcessing objects
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
    public function findOneBySomeField($value): ?CallProcessing
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
