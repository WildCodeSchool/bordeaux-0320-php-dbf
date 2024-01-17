<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @return Client[] Returns an array of Client objects
     */

    public function findAllOrderBy($field, $order = 'ASC', $limit = null)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.' . $field, $order);
        if ($limit) {
            $query->setMaxResults($limit);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param int $clientId
     * @return mixed
     */
    public function setPhoneToNull($clientId)
    {
        return $this->createQueryBuilder('c')
            ->update('App\Entity\Client', 'c')
            ->where('c.id = :val')
            ->setParameter(':val', $clientId)
            ->set('c.phone', ':emptyPhone')
            ->setParameter(':emptyPhone', '00000000')
            ->getQuery()
            ->execute();
    }

    public function getOldClients()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $query = 'select * from client where id not in (select client_id from `call`)';
        $stmt = $conn->prepare($query);
        return $stmt->executeQuery()->fetchAllAssociative();
    }
}
