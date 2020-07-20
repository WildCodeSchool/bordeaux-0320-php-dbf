<?php


namespace App\DataFixtures;

use App\Entity\RecallPeriod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecallPeriodFixtures extends Fixture
{
    const PERIODS = [
        ['Avant', 'avant'],
        ['Autour de', 'autourde'],
        ['après', 'apres'],
        ['dès que possible', 'desquepossible'],
        ['urgent', 'urgent'],
    ];

    public function load(ObjectManager $manager)
    {
        $key=0;
        foreach (self::PERIODS as $recallPeriod) {
            $period = new RecallPeriod();
            $period->setName($recallPeriod[0]);
            $period->setIdentifier($recallPeriod[1]);
            $manager->persist($period);
            $this->addReference('period_' . $key, $period);
            $key++;
        }
        $manager->flush();
    }
}
