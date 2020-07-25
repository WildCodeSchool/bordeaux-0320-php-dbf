<?php

namespace App\Repository;

use App\Entity\ConcessionHead;
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
        return $this->createQueryBuilder('sh')
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
    }

    public function deleteResponsabilities($user, $concession)
    {
        $query = $this->createQueryBuilder('sh')
            ->where('sh.user = :u')->setParameter('u', $user)
            ->join(Service::class, 'se', Join::WITH, 'se.id = sh.service')
            ->andWhere('se.concession = :c')->setParameter('c', $concession)
            ->join(Concession::class, 'co', Join::WITH, 'co.id = se.concession')
            ->getQuery()->getResult();
        dd($query);

        $query->execute();
    }

    public function getServiceHeadInOneService(User $user)
    {
        $query = $this->createQueryBuilder('sh')
            ->Where('sh.user = :u')->setParameter('u', $user)
            ->getQuery()
            ->getResult();

        return $query;
    }

    public function getAllServiceHeadsInConcession($user, $concessionHead)
    {
        return $this->createQueryBuilder('sh')
            ->join(Service::class, 'se', Join::WITH, 'se.id = sh.service')
            ->join(Concession::class, 'co', Join::WITH, 'se.concession = co.id')
            ->join(ConcessionHead::class, 'ch', Join::WITH, 'ch.concession = co.id')
            ->where('ch.user = :user')->setParameter('user', $user)
            ->andWhere('ch.id = :concessionHeadId')->setParameter('concessionHeadId', $concessionHead)
            ->getQuery()->getResult();
    }
}
