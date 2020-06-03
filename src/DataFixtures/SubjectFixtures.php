<?php


namespace App\DataFixtures;

use App\Entity\Subject;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubjectFixtures extends Fixture
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

    const CITY_REF = [null, 0, 1, 2, 3];

    public function load(ObjectManager $manager)
    {
        foreach (self::SUBJECTS as $value) {
            $subject = new Subject();
            $subject->setName($value[0]);
            $subject->setIsForAppWorkshop(rand(0, 1));
            $subject->setCity($this->getReference());

            $manager->persist($subject);
            $this->addReference('city_' . self::CITY_REF[rand(0, 4)], $subject);
        }
        $manager->flush();
    }
}
