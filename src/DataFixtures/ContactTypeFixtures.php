<?php


namespace App\DataFixtures;

use App\Entity\ContactType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContactTypeFixtures extends Fixture
{

    const CONTACT_TYPES = [
        ['Contact établi', 'contact'],
        ['Message 1', 'msg1'],
        ['Message 2', 'msg2'],
        ['Message 3', 'msg3'],
        ['Abandon', 'abandon'],
        ['Non éligible', 'nl'],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CONTACT_TYPES as $value) {
            $contactType = new ContactType();
            $contactType->setName($value[0]);
            $contactType->setIdentifier($value[1]);
            $manager->persist($contactType);
        }
        $manager->flush();
    }
}
