<?php

namespace App\Repository;

use App\Entity\CallProcessing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallProcessing|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallProcessing|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallProcessing[]    findAll()
 * @method CallProcessing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallProcessingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallProcessing::class);
    }

    public function findLastProcessForCall($callId): ?CallProcessing
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.referedCallId = :callId')
            ->setParameter('callId', $callId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findCallProcessingForUser($user)
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
