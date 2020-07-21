<?php

namespace App\Repository;

use App\Entity\ContactType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactType[]    findAll()
 * @method ContactType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactType::class);
    }
}
