<?php


namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class VehicleFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 30; $i++) {
            $vehicle = new Vehicle();
            $plaque = str_shuffle('AZQ') .  str_shuffle('692') .  str_shuffle('HRY');
            $vehicle->setClient($this->getReference('client_'. $i));
            $vehicle->setImmatriculation($plaque);
            $vehicle->setChassis(rand(1000000, 150000000));
            $vehicle->setHasCome(rand(0, 2));
            $vehicle->setCreatedAt($faker->dateTime);
            $manager->persist($vehicle);
            $this->addReference('vehicle_' . $i, $vehicle);
        }
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [ClientFixtures::class];
    }
}
