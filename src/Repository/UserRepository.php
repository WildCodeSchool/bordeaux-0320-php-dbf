<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Concession;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, SluggerInterface $slugger)
    {
        parent::__construct($registry, User::class);
        $this->slugger = $slugger;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findWithCity(int $id)
    {
        return $this->createQueryBuilder('u')
            ->select('u, s, conc, c')
            ->join('u.service', 's')
            ->join('s.concession', 'conc')
            ->join('conc.town', 'c')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findAllInCity()
    {
        return $this->createQueryBuilder('u')
            ->select('u, s, conc, c, CASE WHEN EXISTS (
            SELECT 1
            FROM App:Call as ca
            WHERE ca.recipient = u.id AND ca.isProcessEnded IS NULL
        ) THEN true ELSE false END AS hasCalls')
            ->join('u.service', 's')
            ->join('s.concession', 'conc')
            ->join('conc.town', 'c')
            ->orderBy('u.lastname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByName($name = null)
    {
        $slug = (string)$this->slugger->slug($name);
        return $this->createQueryBuilder('u')
            ->select('u, s, conc, c')
            ->where('u.lastname LIKE :name')
            ->orWhere('u.lastname LIKE :slug')
            ->orWhere('u.firstname LIKE :name')
            ->orWhere('u.firstname LIKE :slug')
            ->setParameter('name', $name . '%')
            ->setParameter('slug', $slug . '%')
            ->join('u.service', 's')
            ->join('s.concession', 'conc')
            ->join('conc.town', 'c')
            ->orderBy('u.lastname', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findOperationnalUsers()
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.canBeRecipient = true')
            ->orderBy('u.lastname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOperationnalUsersInService($service)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.canBeRecipient = true')
            ->andWhere('u.service = :service')
            ->setParameter('service', $service)
            ->orderBy('u.lastname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findAllOrderBy($field, $order = 'ASC', $limit = null)
    {
        $query = $this->createQueryBuilder('u')
            ->orderBy('u.' . $field, $order);
        if ($limit) {
            $query->setMaxResults($limit);
        }
        return $query->getQuery()->getResult();
    }

    public function getRandomUser()
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.canBeRecipient = true')
            ->join(Service::class, 'se', Join::WITH, 'se.id = u.service')
            ->join(Concession::class, 'co', Join::WITH, 'se.concession = co.id')
            ->join(City::class, 'ci', Join::WITH, 'co.town = ci.id')
            ->andWhere('ci.identifier = :cell')
            ->setParameter('cell', 'PHONECITY')
            ->getQuery()
            ->getResult()
            ;
        if ($query) {
            shuffle($query);
            return $query[0];
        }
    }


    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.lastname', 'ASC');
    }
}
