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
        $results = $this->createQueryBuilder('c')
            ->where('c.referedCall = :callId')
            ->setParameter('callId', $callId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
        return $results[0] ?? null;
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
