<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\CityHead;
use App\Entity\Concession;
use App\Entity\ConcessionHead;
use App\Entity\Service;
use App\Entity\ServiceHead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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
    public function getAllConcessionHeadsInCity($user, $cityHeadId)
    {
        return $this->createQueryBuilder('ch')
            ->join(Concession::class, 'co', Join::WITH, 'ch.concession = co.id')
            ->join(City::class, 'ci', Join::WITH, 'co.town = ci.id')
            ->join(CityHead::class, 'cih', Join::WITH, 'ci.id = cih.city')
            ->where('cih.user = :user')->setParameter('user', $user)
            ->andWhere('cih.id = :cityHeadId')->setParameter('cityHeadId', $cityHeadId)
            ->getQuery()->getResult();
    }
}
