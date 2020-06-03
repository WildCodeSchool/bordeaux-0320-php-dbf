<?php


namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    const NUMBER_OF_USERS = 30;

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < self::NUMBER_OF_USERS; $i++) {
            $user = new User();
            $user->setLastname($faker->name);
            $user->setFirstname($faker->firstName);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setPassword('12345');
            $user->setCreatedAt($faker->dateTime);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }
        $manager->flush();
    }
}
