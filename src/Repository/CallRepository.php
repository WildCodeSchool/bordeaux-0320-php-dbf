<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Call;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    const TUPLES_FOR_SEARCH_WITH_TEXT = [
        'email',
        'phone',
        'name',
        'immatriculation',
        'chassis',
        'freeComment',
        'commentTransfer',
    ];
    const TUPLES_BELONG_TO_WHICH_TABLE =[
        'email'=> 'cl',
        'phone' => 'cl',
        'name' => 'cl',
        'immatriculation'=> 'v',
        'chassis'=> 'v',
        'freeComment'=> 'c',
        'commentTransfer'=> 'ct',
    ];
    const TUPLES_IN_SELECT_AWAY_FROM_CALL = [
        'hasCome',
        'contactType',
        'town',
        'concession',
    ];
    const TUPLES_IN_SELECT_BELONG_TO_WHICH_TABLE = [
        'hasCome'=> 'v',
        'contactType'=>'cp',
        'town'=>'concession',
        'concession'=>'serv',
    ];

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
            ->andWhere('c.isProcessEnded IS NULL')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findCallsAddedToday($author)
    {

        $qb = $this->createQueryBuilder('c')
            ->Where('c.author = :author')
            ->setParameter('author', $author)
            ->andWhere('c.createdAt >= CURRENT_DATE()')
            ->orderBy('c.createdAt', 'DESC')
            ->join('c.client', 'cl')->addSelect('cl')
            ->join('cl.civility', 'civ')->addSelect('civ')
            ->join('c.recallPeriod', 'rp')->addSelect('rp')
            ->join('c.comment', 'co')->addSelect('co')
            ->join('c.subject', 'subj')->addSelect('subj')
            ->join('c.vehicle', 'v')->addSelect('v')
            ->setMaxResults(50)
            ->getQuery();

        return $qb->execute();
    }

    public function allCallsByUser($recipient)
    {

        return $this->createQueryBuilder('c')
            ->Where('c.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->innerJoin('c.recipient', 'r')
            ->addSelect('r')
            ->innerJoin('c.recallPeriod', 'rp')
            ->addSelect('rp')
            ->innerJoin('c.subject', 's')
            ->addSelect('s')
            ->innerJoin('c.comment', 'co')
            ->addSelect('co')
            ->innerJoin('c.vehicle', 've')
            ->addSelect('ve')
            ->innerJoin('c.service', 'se')
            ->addSelect('se')
            ->innerJoin('c.client', 'cl')
            ->addSelect('cl')
            ->innerJoin('c.author', 'au')
            ->addSelect('au')
            ->andWhere('c.isProcessEnded IS NULL')
            ->orderBy('c.isUrgent', 'DESC')
            ->addOrderBy('c.recallDate, c.recallHour', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function callsToProcessByUser($recipient)
    {
        $queryRecipient = $this->createQueryBuilder('c')
            ->Where('c.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->innerJoin('c.recipient', 'r')
            ->addSelect('r')
            ->innerJoin('c.recallPeriod', 'rp')
            ->addSelect('rp')
            ->innerJoin('c.subject', 's')
            ->addSelect('s')
            ->innerJoin('c.comment', 'co')
            ->addSelect('co')
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NULL')
            ->orderBy('c.isUrgent', 'DESC')
            ->addOrderBy('c.recallDate, c.recallHour', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        return array_merge($queryRecipient);
    }

    public function callsToProcessByService(Service $service)
    {
        $queryService = $this->createQueryBuilder('c')
            ->Where('c.service = :service')
            ->setParameter('service', $service)
            ->innerJoin('c.recallPeriod', 'rp')
            ->addSelect('rp')
            ->innerJoin('c.subject', 's')
            ->addSelect('s')
            ->innerJoin('c.comment', 'co')
            ->addSelect('co')
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NULL')
            ->orderBy('c.isUrgent', 'DESC')
            ->addOrderBy('c.recallDate, c.recallHour', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $queryService;
    }

    public function lastCallToProcessByUser($recipient)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->innerJoin('c.recipient', 'r')
            ->addSelect('r')
            ->innerJoin('c.recallPeriod', 'rp')
            ->addSelect('rp')
            ->innerJoin('c.subject', 's')
            ->addSelect('s')
            ->innerJoin('c.comment', 'co')
            ->addSelect('co')
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NULL')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function callsInProcessByUser($recipient)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->innerJoin('c.recipient', 'r')
            ->addSelect('r')
            ->innerJoin('c.recallPeriod', 'rp')
            ->addSelect('rp')
            ->innerJoin('c.subject', 's')
            ->addSelect('s')
            ->innerJoin('c.comment', 'co')
            ->addSelect('co')
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NOT NULL')
            ->orderBy('c.recallDate, c.recallHour', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getNewCallsForUser($recipient, $lastId)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->innerJoin('c.recipient', 'r')
            ->addSelect('r')
            ->innerJoin('c.recallPeriod', 'rp')
            ->addSelect('rp')
            ->innerJoin('c.subject', 's')
            ->addSelect('s')
            ->innerJoin('c.comment', 'co')
            ->addSelect('co')
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NULL')
            ->andWhere('c.id > :sessionCall')
            ->setParameter('sessionCall', $lastId)
            ->orderBy('c.recallDate', 'DESC')
            ->addOrderBy('c.recallHour', 'DESC')
            ->addOrderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getCallsAddedByUserToday(User $user)
    {
        $start = new DateTime(date('Y-m-d 00:00:00'));
        $end = new DateTime(date('Y-m-d 23:59:00'));
        return $this->createQueryBuilder('c')
            ->Where('c.author = :auth')
            ->setParameter('auth', $user)
            ->andWhere('c.createdAt >= :start')
            ->setParameter('start', $start)
            ->andWhere('c.createdAt <= :end')
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult()
            ;
    }


    public function findSearch(SearchData $searchData): array
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.client', 'cl')->addSelect('cl')
            ->join('c.vehicle', 'v')->addSelect('v')
            ->leftJoin('c.callProcessings', 'cp')->addSelect('cp')
            ->leftJoin('c.callTransfers', 'ct')->addSelect('ct')
            ->where('c.recipient IS NOT NULL')
        ;

        $this->addSearchParametersToQuery($searchData, $query);

        if (!empty($searchData->getDateFrom()) && !empty($searchData->getDateTo())) {
            $query = $query
                ->andWhere('c.createdAt BETWEEN :from AND :to')
                ->setParameter('from', $searchData->getDateFrom()->format('Y-m-d') . ' 00:00:00')
                ->setParameter('to', $searchData->getDateTo()->format('Y-m-d') . ' 23:59:59')
            ;
        }

        return $query->getQuery()->getResult();
    }

    public function getNotInProcessCallsByService($service)
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->andWhere('c.service = :se')
            ->setParameter('se', $service)
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
    public function getInProcessCallsByService($service)
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->andWhere('c.service = :se')
            ->setParameter('se', $service)
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }


    private function queryWithLikeMaker($property, &$query, $value)
    {
        return $query
            ->andWhere(self::TUPLES_BELONG_TO_WHICH_TABLE[$property] . '.' . $property . ' LIKE  :' . $property)
            ->setParameter(
                $property,
                '%' . $value . '%'
            );
    }

    private function queryWithEqualMakerInCallTable($property, &$query, $value)
    {
        return $query->andWhere('c.' . $property . ' = :' . $property)
            ->setParameter($property, $value);
    }

    private function queryWithEqualMakerElsewhere($property, &$query, $value)
    {
        return $query->andWhere(self::TUPLES_IN_SELECT_BELONG_TO_WHICH_TABLE[$property] . '.' .
            $property . ' = :' . $property)
            ->setParameter($property, $value);
    }

    private function addSearchParametersToQuery($searchData, &$query)
    {
        foreach ($searchData as $property => $value) {
            if (!empty($searchData->$property)) {
                if (in_array($property, self::TUPLES_FOR_SEARCH_WITH_TEXT)) {
                    $query = $this->queryWithLikeMaker($property, $query, $value);
                } elseif (in_array($property, self::TUPLES_IN_SELECT_AWAY_FROM_CALL)) {
                   //autres table que call
                    $query = $this->queryWithEqualMakerElsewhere($property, $query, $value);
                } else {
                    $query = $this->queryWithEqualMakerInCallTable($property, $query, $value);
                }
            }
        }
        return $query;
    }



    /**

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
