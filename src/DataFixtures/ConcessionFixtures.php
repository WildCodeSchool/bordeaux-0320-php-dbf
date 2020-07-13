<?php


namespace App\DataFixtures;

use App\Entity\Concession;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ConcessionFixtures extends Fixture implements DependentFixtureInterface
{
    const CONCESSIONS = [
        'Cellule Téléphonique' =>[
            'town' => 'city_3',
            'name' => 'Cellule Téléphonique',
            'address' => ' ',
            'postcode' => '33000',
            'city' => ' ',
            'brand' =>'  ',
            'phone' => ' ',
        ],

        'La Teste' =>[
            'town' => 'city_0',
            'name' => 'La Teste',
            'address' => '70 Rue Lagrua',
            'postcode' => '33260',
            'city' => 'La Teste de Buch',
            'brand' =>' Audi',
            'phone' => '05 57 73 66 99',
        ],
        'Mérignac' => [
            'town' => 'city_0',
            'name' => 'Mérignac',
            'address' => '34 avenue Roland Garros',
            'postcode' => '33700',
            'city' => 'Mérignac',
            'brand' =>' Audi',
            'phone' => '05 56 02 71 71',
        ],
        'Artigues' => [
            'town' => 'city_0',
            'name' => 'Artigues',
            'address' => '9 Avenue du Millac',
            'postcode' => '33370',
            'city' => 'Artigues Près Bordeaux',
            'brand' =>' Audi',
            'phone' => '05 56 86 75 40',
        ],
        'Etats-Unis' => [
            'town' => 'city_1',
            'name' => 'Etats-Unis',
            'address' => '344 avenue des États-Unis Toulouse Nord • Sortie 33 B Sesquières Lalande',
            'postcode' => '31201',
            'city' => 'Toulouse Cedex 2',
            'brand' =>' Volkswagen',
            'phone' => '05 62 75 87 58',
        ],
        'Labège' => [
            'town' => 'city_1',
            'name' => 'Labège',
            'address' => '2686, route de baziège',
            'postcode' => '31670',
            'city' => 'Labège',
            'brand' =>' Volkswagen',
            'phone' => '05 61 36 30 00',
        ],
        'Espagne' => [
            'town' => 'city_1',
            'name' => 'Espagne',
            'address' => '290 Rue Leon Joulin',
            'postcode' => '31100',
            'city' => 'Toulouse',
            'brand' =>' Volkswagen',
            'phone' => '05 61 44 44 44',
        ],
        'Saint Clément de Rivière' => [
            'town' => 'city_2',
            'name' => 'Saint Clément de Rivière',
            'address' => 'Impasse des églantiers',
            'postcode' => '34980',
            'city' => 'Saint Clément de Rivière',
            'brand' =>' Volkswagen Audi',
            'phone' => '04 67 61 02 94',
        ],
        'Le Crès' => [
            'town' => 'city_2',
            'name' => 'Le Crès',
            'address' => '145, route de Nîmes',
            'postcode' => '34920',
            'city' => 'Le Crès',
            'brand' =>'Volkswagen',
            'phone' => '04 67 70 50 00',
        ],
        'Sète' => [
            'town' => 'city_2',
            'name' => 'Sète',
            'address' => '13, quai Louis Pasteur',
            'postcode' => '34200',
            'city' => 'Sète',
            'brand' =>'Volkswagen',
            'phone' => '04 67 46 07 00',
        ],
        'Tournezy VW' => [
            'town' => 'city_2',
            'name' => 'Tournezy VW',
            'address' => '3160 Avenue de Maurin Tournezy',
            'postcode' => '34076',
            'city' => 'Montpellier Cedex 3',
            'brand' =>'Volkswagen',
            'phone' => '04 67 07 83 83',
        ],
        'Tournezy Audi' => [
            'town' => 'city_2',
            'name' => 'Tournezy Audi',
            'address' => '3160 Avenue de Maurin',
            'postcode' => '34076',
            'city' => 'Montpellier Cedex 3',
            'brand' =>'Volkswagen',
            'phone' => '04 67 07 83 93',
        ],
    ];


    public function getDependencies()
    {
        return [CityFixtures::class];
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $key = 0;
        foreach (self::CONCESSIONS as $city => $data) {
            $concession = new Concession();
            $concession->setName($city);
            $concession->setTown($this->getReference($data['town']));
            $concession->setAddress($data['address']);
            $concession->setPostcode($data['postcode']);
            $concession->setCity($data['city']);
            $concession->setBrand($data['brand']);
            $concession->setPhone($data['phone']);
            $manager->persist($concession);
            $this->addReference('concession_' . $key, $concession);
            $key++;
        }
        $manager->flush();
    }
}
