<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function getCarBodyWorkshops()
    {
        return $this->createQueryBuilder('s')
            ->Where('s.isCarBodyWorkshop is not NULL')
            ->andWhere('s.isCarBodyWorkshop = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByConcession()
    {
        return $this->createQueryBuilder('s')
            ->join('s.concession', 'co')
            ->orderBy('co.name', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getServicesToContact($concession)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = "SELECT * FROM service WHERE (is_direction IS NULL or is_direction = 0) and concession_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $concession->getId());
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getConcessionCarBodyWorkshops($concession, $brand)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.isCarBodyWorkshop is not NULL')
            ->andWhere('s.isCarBodyWorkshop = :val')
            ->setParameter('val', 1)
            ->andWhere('s.brand = :brand')
            ->setParameter('brand', $brand)
            ->andWhere('s.concession = :concession')
            ->setParameter('concession', $concession->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getNearestCarBodyWorkshop($concession)
    {
        if ($concession->getNearestCarBodyWorkshop()) {
            return $this->findOneById($concession->getNearestCarBodyWorkshop());
        }
        return null;
    }

    public function removeAllServicesInConcession($concession)
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.concession = :concession')
            ->setParameter('concession', $concession)
            ->getQuery()
            ->getResult();
    }
}
