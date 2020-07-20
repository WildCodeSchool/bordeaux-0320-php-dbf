<?php


namespace App\DataFixtures;

use App\Entity\Civility;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CivilityFixtures extends Fixture
{
    const CIVILITY = [
        'Mr',
        'Mme',
        'Ste'
    ];

    /**
     * @inheritDoc
     */
    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        foreach (self::CIVILITY as $key => $civility) {
            $civil = new Civility();
            $civil->setName($civility);
            $manager->persist($civil);
            $this->addReference('civility_' . $key, $civil);
        }
        $manager->flush();
    }
}
