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

    public function removeOldCalls($beforeDate)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.createdAt < :before')
            ->setParameter('before', $beforeDate)
            ->getQuery()
            ->getResult();

    }

    public function getAllTransfersForUser($user)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.referedCall', 'call')
            ->where('call.recipient = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
            ;
    }
}
