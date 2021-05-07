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

    public function findByBrand($brand)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.brand LIKE :brand')
            ->setParameter('brand', '%' . $brand . '%')
            ->andWhere('t.identifier is NULL')
            ->innerJoin('c.town', 't')
            ->addSelect('t.name as cityName')->addSelect('t.identifier')
            ->orderBy('t.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findAllConcessions()
    {
        return $this->createQueryBuilder('c')
            ->where('t.identifier is NULL')
            ->innerJoin('c.town', 't')
            ->addSelect('t.name as cityName')
            ->addSelect('t.identifier')
            ->orderBy('t.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllConcessionsOrderByTown()
    {
        return $this->createQueryBuilder('c')
            ->where('t.identifier is NULL')
            ->join('c.town', 't')
            ->orderBy('t.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
