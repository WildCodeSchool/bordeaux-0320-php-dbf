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

            /*
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT
                ci.name city,
                co.name concession,
                sh.service_id,
                se.name service
                FROM service_head sh
                JOIN service se
                ON se.id = sh.service_id
                JOIN concession co
                ON co.id = se.concession_id
                JOIN city ci
                ON ci.id = co.town_id
                WHERE sh.user_id = :u
                ORDER BY ci.name, co.name, se.name';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('u', $user->getId());
        $stmt->execute();
        return $stmt->fetchAll();
            */
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
