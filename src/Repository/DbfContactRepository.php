<?php

namespace App\Repository;

use App\Entity\DbfContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DbfContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method DbfContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method DbfContact[]    findAll()
 * @method DbfContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DbfContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DbfContact::class);
    }

    // /**
    //  * @return DbfContact[] Returns an array of DbfContact objects
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
    public function findOneBySomeField($value): ?DbfContact
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
