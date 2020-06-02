<?php

namespace App\Repository;

use App\Entity\RightByLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RightByLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RightByLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RightByLocation[]    findAll()
 * @method RightByLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RightByLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RightByLocation::class);
    }

    // /**
    //  * @return RightByLocation[] Returns an array of RightByLocation objects
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
    public function findOneBySomeField($value): ?RightByLocation
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
