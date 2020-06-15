<?php


namespace App\DataFixtures;

use DateTime;
use Faker;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Création d’un utilisateur de type “collaborateur”
        $collaborator = new User();
        $collaborator->setEmail('collaborateur@dbf.com');
        $collaborator->setFirstname('John');
        $collaborator->setLastname('Doe');
        $collaborator->setCreatedAt(new DateTime());
        $collaborator->setRoles(['ROLE_COLLABORATOR']);
        $collaborator->setPassword($this->passwordEncoder->encodePassword(
            $collaborator,
            'collabpassword'
        ));

        $manager->persist($collaborator);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setEmail('admin@dbf.com');
        $admin->setFirstname('Admin');
        $admin->setLastname('dbf');
        $admin->setCreatedAt(new DateTime());
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));

        $manager->persist($admin);

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }
}
