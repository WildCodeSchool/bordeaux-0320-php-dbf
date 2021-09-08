<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Call;
use App\Entity\City;
use App\Entity\Concession;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    const PROCESSES = [
        'to process' => 'IS NULL',
        'in process' => 'IS NOT NULL'
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
            ->orderBy('c.id', 'DESC')
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

    public function everyCallsByUser($recipient)
    {

        $limit = new \DateTime('Europe/Paris');
        $limit->sub(new \DateInterval('P30D'));

        return $this->createQueryBuilder('c')
            ->Where('c.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->andWhere('c.isProcessEnded = 1')
            ->andWhere('c.isAppointmentTaken != 1')
            ->andWhere('c.createdAt >= :limit')
            ->setParameter('limit', $limit)
            ->orderBy('c.createdAt', 'DESC')
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
        return $this->createQueryBuilder('c')
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
    }

    public function newCallsToProcessByService(Service $service, $lastCallId)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.service = :service')
            ->setParameter('service', $service)
            ->andWhere('c.id > :lastCall')
            ->setParameter('lastCall', $lastCallId)
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

    public function lastCallToProcessByService($service)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.recipient IS NULL')
            ->andWhere('c.service = :service')
            ->setParameter('service', $service)
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

    public function getCallsForVehicle($vehicle)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.vehicle = :vehicle')
            ->setParameter('vehicle', $vehicle)
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
            ->join(User::class, 'u', Join::WITH, 'u.id = c.recipient')
            ->join(Service::class, 'serv', Join::WITH, 'u.service = serv.id')
            ->addSelect('serv')
            ->join(
                Concession::class,
                'concession',
                Join::WITH,
                'serv.concession= concession.id'
            )
            ->addSelect('concession')
            ->join(City::class, 'city', Join::WITH, 'concession.town = city.id')
            ->addSelect('city')
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

    public function removeOldCalls($beforeDate)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.createdAt < :before')
            ->setParameter('before', $beforeDate)
            ->getQuery()
            ->getResult();

    }

    public function countCallsInService(Service $service, string $processCondition)
    {
        $result = $this->createQueryBuilder('c')
            ->select('count(c.id) as total')
            ->Where('c.recipient IS NOT NULL')
            ->innerJoin('c.recipient', 'r')
            ->innerJoin('r.service', 's')
            ->andWhere('s.id = :sid')
            ->setParameter('sid', $service->getId())
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed ' . self::PROCESSES[$processCondition])
            ->getQuery()
            ->getOneOrNullResult()
            ;
        return $result['total'];
    }

    public function countCallstoTake(Service $service)
    {
        $result = $this->createQueryBuilder('c')
            ->select('count(c.id) as total')
            ->Where('c.recipient IS NULL')
            ->andWhere('c.service = :sid')
            ->setParameter('sid', $service->getId())
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed IS NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;
        return $result['total'];
    }

    public function countCallsInConcession(Concession $concession, string $processCondition)
    {
        $result = $this->createQueryBuilder('c')
            ->select('count(c.id) as total')
            ->Where('c.recipient IS NOT NULL')
            ->innerJoin('c.recipient', 'r')
            ->innerJoin('r.service', 's')
            ->innerJoin('s.concession', 'co')
            ->andWhere('co.id = :coid')
            ->setParameter('coid', $concession->getId())
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed ' . self::PROCESSES[$processCondition])
            ->getQuery()
            ->getOneOrNullResult()
        ;
        return $result['total'];
    }

    public function countCallsInCity(City $city, string $processCondition)
    {
        $result = $this->createQueryBuilder('c')
            ->select('count(c.id) as total')
            ->Where('c.recipient IS NOT NULL')
            ->innerJoin('c.recipient', 'r')
            ->innerJoin('r.service', 's')
            ->innerJoin('s.concession', 'co')
            ->innerJoin('co.town', 't')
            ->andWhere('t.id = :tid')
            ->setParameter('tid', $city->getId())
            ->andWhere('c.isProcessEnded IS NULL')
            ->andWhere('c.isProcessed ' . self::PROCESSES[$processCondition])
            ->getQuery()
            ->getOneOrNullResult()
        ;
        return $result['total'];
    }

    public function removeCallsForUser($user)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.recipient = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function removeCallsWhereUserIsAuthor($user)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.author = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function deleteAllProcessesAndTransfersWhereUserIsConcerned($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $q1 ='DELETE FROM `call_processing` WHERE refered_call_id in (select id from `call` where author_id=?)';
        $stmt = $conn->prepare($q1);
        $stmt->bindValue(1, $user->getId());
        $stmt->execute();

        $q2 ='DELETE FROM `call_transfer` WHERE refered_call_id in (select id from `call` where author_id=?) or by_whom_id=? or to_whom_id=? or from_whom_id=?';
        $stmt = $conn->prepare($q2);
        $stmt->bindValue(1, $user->getId());
        $stmt->bindValue(2, $user->getId());
        $stmt->bindValue(3, $user->getId());
        $stmt->bindValue(4, $user->getId());
        $stmt->execute();
    }

    public function deleteRelictual()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $q1 = 'DELETE FROM `call_processing` WHERE refered_call_id in (select id FROM `call` where author_id in (select id from user where service_id IS NULL) or author_id not in (select id from user))';
        $q2 = 'DELETE FROM `call_transfer` WHERE refered_call_id in (select id FROM `call` where author_id in (select id from user where service_id IS NULL)  or author_id not in (select id from user))';
        $q3 = 'DELETE FROM `call` where author_id in (select id from user where service_id IS NULL) or author_id not in (select id from user)';

        $query = 'select * from vehicle where id not in (select vehicle_id from `call`)';
        $stmt = $conn->prepare($q1);
        $stmt->execute();
        $stmt = $conn->prepare($q2);
        $stmt->execute();
        $stmt = $conn->prepare($q3);
        $stmt->execute();
    }

    public function removeCallsForService($service)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.service = :service')
            ->setParameter('service', $service)
            ->getQuery()
            ->getResult();
    }


}
