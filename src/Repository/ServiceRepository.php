<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function getCarBodyWorkshops()
    {
        return $this->createQueryBuilder('s')
            ->Where('s.isCarBodyWorkshop is not NULL')
            ->andWhere('s.isCarBodyWorkshop = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getResult();
    }
}
