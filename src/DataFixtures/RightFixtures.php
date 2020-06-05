<?php


namespace App\DataFixtures;

use App\Entity\Right;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RightFixtures extends Fixture
{

    const RIGHTS = [
        ['admin', 'admin'],
        ['director', 'director'],
        ['user', 'user'],
        ['intern', 'intern'],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::RIGHTS as $value) {
            $right = new Right();
            $right->setLevel($value[0]);
            $manager->persist($right);
        }
        $manager->flush();
    }
}
