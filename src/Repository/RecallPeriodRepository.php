<?php

namespace App\Repository;

use App\Entity\RecallPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecallPeriod|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecallPeriod|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecallPeriod[]    findAll()
 * @method RecallPeriod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecallPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecallPeriod::class);
    }
}
