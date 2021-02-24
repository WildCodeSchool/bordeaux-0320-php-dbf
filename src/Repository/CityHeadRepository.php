<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\CityHead;
use App\Entity\Concession;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CityHead|null find($id, $lockMode = null, $lockVersion = null)
 * @method CityHead|null findOneBy(array $criteria, array $orderBy = null)
 * @method CityHead[]    findAll()
 * @method CityHead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityHeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityHead::class);
    }

    public function findAllOrderByName() {
        return $this->createQueryBuilder('ch')
            ->innerJoin('ch.user', 'u')
            ->orderBy('u.lastname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function isCityHead(User $user, City $city)
    {
        $result = $this->createQueryBuilder('ch')
            ->where('ch.user = :user')
            ->setParameter('user', $user->getId())
            ->andWhere('ch.city = :city')
            ->setParameter('city', $city->getId())
            ->getQuery()
            ->getResult();
        return ($result) ? true : false;
    }
}
