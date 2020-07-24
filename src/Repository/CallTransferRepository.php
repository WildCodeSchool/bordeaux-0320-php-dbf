<?php

namespace App\Repository;

use App\Entity\CallTransfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallTransfer|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallTransfer|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallTransfer[]    findAll()
 * @method CallTransfer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallTransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallTransfer::class);
    }
}
