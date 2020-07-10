<?php

namespace App\Repository;

use App\Entity\ServiceHead;
use App\Entity\User;
use App\Entity\Service;
use App\Entity\Call;
use App\Entity\Concession;
use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $query = $this->createQueryBuilder('sh');
        $query
            ->select('ci.name city')
            ->select('co.id concession')
            ->select('sh.service')
            ->select('se.name serviceName')
            ->join(Service::class, 'se', 'se.id = sh.service')
            ->join(Concession::class, 'co', 'co.id = se.concession')
            ->join(City::class, 'ci', 'ci.id = co.town')
            ->where('sh.user =:u')
            ->setParameter('u', $user)
            ->getQuery()
            ->getResult()
            ;
    }


/*
    public function getInProcessCalls ($service)
    {
        return $this->createQueryBuilder('call c')
            ->select('count(c.id)')
            ->andWhere('c.service = :service')
            ->setParameter('service', $service)
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NOT NULL')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
*/
    // /**
    //  * @return ServiceHead[] Returns an array of ServiceHead objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ServiceHead
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
