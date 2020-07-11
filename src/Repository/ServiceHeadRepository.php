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
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT
                ci.name city, 
                co.name concession, 
                sh.service_id, 
                se.name service, 
                catp.toprocess toprocess, 
                caip.inprocess inprocess 
                FROM service_head sh 
                JOIN service se 
                ON se.id = sh.service_id 
                JOIN concession co 
                ON co.id = se.concession_id 
                JOIN city ci 
                ON ci.id = co.town_id 
                LEFT JOIN (
                    SELECT count(*) toprocess, service_id  
                    FROM `call` ca 
                    WHERE ca.is_processed 
                    IS NULL GROUP BY service_id
                    ) catp 
                ON catp.service_id = sh.service_id 
                LEFT JOIN (
                    SELECT count(*) inprocess, service_id  
                    FROM `call` ca 
                    WHERE ca.is_processed 
                    IS NOT NULL 
                    AND ca.is_process_ended IS NULL 
                    GROUP BY service_id
                    ) caip 
                ON caip.service_id = sh.service_id 
                WHERE sh.user_id = :u
                ORDER BY ci.name, co.name, se.name';
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('u', $user->getId());
        $stmt->execute();
        dd($stmt->fetchAll());
        return $stmt->fetchAll();
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
