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
    const USERS = [
        [
            'firstname'=>'François',
            'lastname' => 'Munoz',
            'phone'=>'+33 7 46 21 18 36',
        ],
        [
            'firstname'=>'Julien',
            'lastname' => 'Boutin',
            'phone'=>'01 46 11 52 67',
        ],
        [
            'firstname'=>'Michelle',
            'lastname' => 'Bourdin',
            'phone'=>'0699912716',
        ],
        [
            'firstname'=>'Yves',
            'lastname' => 'Neveu',
            'phone'=>'0895234953',
        ],
        [
            'firstname'=>'Isaac',
            'lastname' => 'Aubert',
            'phone'=>'+33 5 10 39 28 46',
        ],
        [
            'firstname'=>'Alphonsine',
            'lastname' => 'Caron',
            'phone'=>'04 96 13 33 92',
        ],
        [
            'firstname'=>'Raymond',
            'lastname' => 'Pinto',
            'phone'=> '06 21 64 56 64'
        ],
    ];



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
        $admin->setService($this->getReference('services_1'));
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

        $key = 0;
        $faker = Faker\Factory::create('fr_FR');
        foreach (self::USERS as $data => $datum) {
            $user = new User();
            $user->setFirstname($datum['firstname']);
            $user->setLastname($datum['lastname']);
            $user->setEmail($datum['firstname'] . '.' . $datum['lastname'] . '@dbf.com');
            $user->setPhone($datum['phone']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                '123456'
            ));
            $user->setCreatedAt($faker->dateTime);
            $user->setRoles(['ROLE_COLLABORATOR']);
            $user->setService($this->getReference('services_' . $key));
            $manager->persist($user);
            $this->addReference('user_' . $key, $user);
            $key ++;
        }

        /**
         * $faker = Faker\Factory::create('fr_FR');
        for $i = 0; $i (< 10; $i++) {
            $user = new User();
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                '123456'
            ));
            $user->setCreatedAt($faker->dateTime);
            $user->setRoles(['ROLE_COLLABORATOR']);
            $user->setService($this->getReference('services_' . rand(0, 9)));
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
**/
            // Sauvegarde des  utilisateurs :
            $manager->flush();
    }
}
