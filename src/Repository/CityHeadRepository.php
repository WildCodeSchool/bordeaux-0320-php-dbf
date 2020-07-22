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
}
