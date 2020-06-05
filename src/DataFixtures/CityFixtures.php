<?php


namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    const CITIES = [
        'Bordeaux',
        'Toulouse',
        'Montpellier',
        'Cellule Téléphonique',
    ];
    public function load(ObjectManager $manager)
    {
        $key = 0;
        foreach (self::CITIES as $cityName) {
            $city = new City();
            $city->setName($cityName);
            $manager->persist($city);
            $this->addReference('city_' . $key, $city);
            $key++;
        }
        $manager->flush();
    }
}
