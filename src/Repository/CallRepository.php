<?php

namespace App\Repository;

use App\Entity\Call;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use \DateTime;
use \DateInterval;

/**
 * @method Call|null find($id, $lockMode = null, $lockVersion = null)
 * @method Call|null findOneBy(array $criteria, array $orderBy = null)
 * @method Call[]    findAll()
 * @method Call[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Call::class);
    }


    public function callsOnTheWayForClient($clientId)
    {
        $date = new DateTime('now');
        $dateLimit = $date->sub(new DateInterval('P7D'));

        return $this->createQueryBuilder('c')
            ->Where('c.client = :client')
            ->setParameter('client', $clientId)
            ->andWhere('c.createdAt >= :limitDate')
            ->setParameter('limitDate', $dateLimit)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findCallsAddedToday($author)
    {
        $date = new DateTime('now');
        $dateLimit = $date->sub(new DateInterval('P1D'));

        $qb = $this->createQueryBuilder('c')
            ->Where('c.author = :author')
            ->setParameter('author', $author)
            ->andWhere('c.createdAt >= :limitDate')
            ->setParameter('limitDate', $dateLimit)
            ->orderBy('c.createdAt', 'DESC')
            ->join('c.client', 'cl')->addSelect('cl')
            ->join('cl.civility', 'civ')->addSelect('civ')
            ->join('c.service', 's')->addSelect('s')
            ->join('s.concession', 'concession')->addSelect('concession')
            ->join('c.recallPeriod', 'rp')->addSelect('rp')
            ->join('c.comment', 'co')->addSelect('co')
            ->join('c.subject', 'subj')->addSelect('subj')
            ->join('c.vehicle', 'v')->addSelect('v')
            ->getQuery();

        return $qb->execute();
    }


    // /**
    //  * @return Call[] Returns an array of Call objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Call
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
