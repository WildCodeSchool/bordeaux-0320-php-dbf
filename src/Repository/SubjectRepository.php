<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function getAllNotHidden($cityId)
    {
        $qb = $this->createQueryBuilder('s');
        return $qb
            ->where('s.city = :city')
            ->setParameter('city', $cityId)
            ->andWhere('s.isHidden is NULL OR s.isHidden = 0')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
