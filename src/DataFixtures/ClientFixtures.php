<?php


namespace App\DataFixtures;

use App\Entity\Civility;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {
            $client = new Client();
            $client->setName($faker->name);
            $client->setPhone($faker->phoneNumber);
            $client->setPhone2($faker->phoneNumber);
            $client->setEmail($faker->email);
            $client->setPostcode(intval($faker->postcode));
            $client->setCivility($this->getReference('civility_' . rand(0, 2)));
            $client->setCreatedAt($faker->dateTime);
            $manager->persist($client);
            $this->addReference('client_' . $i, $client);
        }
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
         return [CivilityFixtures::class];
    }
}
