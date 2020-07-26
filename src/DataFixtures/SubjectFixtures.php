<?php


namespace App\DataFixtures;

use App\Entity\Subject;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubjectFixtures extends Fixture implements DependentFixtureInterface
{

    const SUBJECTS = [
        ['Rendez-vous Atelier Artigues', 'rdvatar'],
        ['Rendez-vous Atelier Mérignac', 'rdvatmer'],
        ['Rendez-vous Atelier Lateste', 'rdvatlat'],
        ['Rendez-vous Carrosserie Artigues', 'rdvcarar'],
        ['Rendez-vous Carrosserie Mérignac', 'rdvcarmer'],
        ['Rendez-vous Carrosserie Lateste', 'rdvcarlat'],
        ['Tarifs', 'tarif'],
    ];

    const CITY_REF = [0, 1, 2, 3];

    public function load(ObjectManager $manager)
    {
        $key = 0;
        foreach (self::SUBJECTS as $value) {
            $subject = new Subject();
            $subject->setName($value[0]);
            $subject->setIsForAppWorkshop(rand(0, 1));
            $subject->setCity($this->getReference('city_' . self::CITY_REF[rand(0, 3)]));
            $manager->persist($subject);
            $this->addReference('subject_' . $key, $subject);
            $key++;
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [CityFixtures::class];
    }
}
