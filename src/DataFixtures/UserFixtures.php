<?php


namespace App\DataFixtures;

use DateTime;
use Faker;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getDependencies()
    {
        return [ServiceFixtures::class];
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
        $collaborator->setService($this->getReference('services_4'));

        $collaborator->setPassword($this->passwordEncoder->encodePassword(
            $collaborator,
            'collabpassword'
        ));

        $manager->persist($collaborator);

        // Création d’un utilisateur de type “admin”
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

        // Création d’un utilisateur de test
        $test = new User();
        $test->setEmail('test@dbf.com');
        $test->setFirstname('test');
        $test->setLastname('');
        $test->setCreatedAt(new DateTime());
        $test->setRoles(['ROLE_COLLABORATOR']);
        $test->setPassword($this->passwordEncoder->encodePassword(
            $test,
            'test'
        ));

        $manager->persist($test);

        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setPassword('12345');
            $user->setCreatedAt($faker->dateTime);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);

            // Sauvegarde des  utilisateurs :
            $manager->flush();
        }
    }
}
