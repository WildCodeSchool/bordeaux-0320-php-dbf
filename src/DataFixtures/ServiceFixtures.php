<?php


namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture implements DependentFixtureInterface
{
    const SERVICES = [
        'Mécanique',
        'Carrosserie',
        'Financement',
        'Pièces détachées',
        'Marketing',
        'Location Courte Durée (RENT)',
        'Service Commercial Véhicules Neufs',
        'Service Commercial Véhicules Occasions',
        'Secrétariat Véhicules Neufs',
        'Secrétariat Véhicules Occasions',
        'Service Qualité',
    ];

    public function getDependencies()
    {
        return [ConcessionFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $key = 0;
        for ($i = 0; $i < 10; $i++) {
            foreach (self::SERVICES as $serviceName) {
                $service = new Service();
                $service->setName($serviceName);
                $service->setConcession($this->getReference('concession_' .$i));
                $manager->persist($service);
                $this->addReference('services_' . $key, $service);
                $key++;
            }
        }
        $manager->flush();
    }
}
