<?php


namespace App\Repository;


use App\Entity\Call;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ResetRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Call::class);
    }

    public function resetDatabase()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $q1 = 'DELETE FROM `call_processing`';
        $stmt = $conn->prepare($q1);
        $stmt->execute();

        $q2 = 'DELETE FROM `call_transfer`';
        $stmt = $conn->prepare($q2);
        $stmt->execute();

        $q3 = 'DELETE FROM `call`';
        $stmt = $conn->prepare($q3);
        $stmt->execute();

        $q4 = 'DELETE FROM `vehicle`';
        $stmt = $conn->prepare($q4);
        $stmt->execute();

        $q5 = 'DELETE FROM `client`';
        $stmt = $conn->prepare($q5);
        $stmt->execute();

    }

}
