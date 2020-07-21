<?php

namespace App\Repository;

use App\Entity\ConcessionHead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConcessionHead|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConcessionHead|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConcessionHead[]    findAll()
 * @method ConcessionHead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcessionHeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConcessionHead::class);
    }
}
