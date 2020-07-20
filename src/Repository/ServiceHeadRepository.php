<?php

namespace App\Repository;

use App\Entity\ServiceHead;
use App\Entity\User;
use App\Entity\Service;
use App\Entity\Concession;
use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceHead|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceHead|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceHead[]    findAll()
 * @method ServiceHead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceHeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceHead::class);
    }

    public function getHeadServiceCalls(User $user)
    {
        $query = $this->createQueryBuilder('sh')
            ->Where('sh.user = :u')
            ->setParameter('u', $user->getId())
            ->join(Service::class, 'se', Join::WITH, 'se.id = sh.service')
            ->addSelect('se.name service')
            ->addSelect('se.id service_id')
            ->join(Concession::class, 'co', Join::WITH, 'se.concession = co.id')
            ->addSelect('co.name concession')
            ->join(City::class, 'ci', Join::WITH, 'co.town = ci.id')
            ->addSelect('ci.name city')
            ->addOrderBy('city', 'ASC')
            ->addOrderBy('concession', 'ASC')
            ->addOrderBy('service', 'ASC')
            ->getQuery()->getResult();
        return $query;
    }

    public function getServiceHeadsInOneConcession(User $user, Service $service)
    {
        $query = $this->createQueryBuilder('sh')
            ->Where('sh.user = :u')->setParameter('u', $user)
            ->andWhere('sh.service = :s')->setParameter('s', $service)
            ->getQuery()
            ->getResult();

        return $query;
    }
}
