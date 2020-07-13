<?php


namespace App\DataFixtures;

use App\Entity\Service;
use App\Entity\ServiceHead;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ServiceHeadFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [ServiceFixtures::class, UserFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $userAdmin = $this->getReference('adminUser');
        $collaborator = $this->getReference('collaboratorUser');

        for ($i = 1; $i < 4; $i++) {
            $serviceHead = new ServiceHead();
            $serviceHead->setUser($collaborator);
            $serviceHead->setService($this->getReference('services_' . $i));
            $manager->persist($serviceHead);
        }

        for ($i = 0; $i < 14; $i++) {
            $serviceHead = new ServiceHead();
            $serviceHead->setUser($userAdmin);
            $serviceHead->setService($this->getReference('services_' . $i));
            $manager->persist($serviceHead);
        }

        $manager->flush();
    }
}
