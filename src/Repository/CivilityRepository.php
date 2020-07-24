<?php

namespace App\Repository;

use App\Entity\Civility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Civility|null find($id, $lockMode = null, $lockVersion = null)
 * @method Civility|null findOneBy(array $criteria, array $orderBy = null)
 * @method Civility[]    findAll()
 * @method Civility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CivilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Civility::class);
    }
}
