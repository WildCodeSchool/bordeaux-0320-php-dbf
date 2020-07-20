<?php

namespace App\Repository;

use App\Entity\CityHead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CityHead|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityHead|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityHead[]    findAll()
 * @method CityHead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityHeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityHead::class);
    }

    // /**
    //  * @return CityHead[] Returns an array of CityHead objects
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
    public function findOneBySomeField($value): ?CityHead
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
