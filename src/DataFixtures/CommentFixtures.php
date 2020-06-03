<?php


namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture
{

    const COMMENTS = [
        ['Rev 30000', 'r30'],
        ['Rev 60000', 'r60'],
        ['Rev 90000', 'r90'],
        ['Rev 120000', 'r120'],
        ['Rev 150000', 'r150'],
        ['Freins', 'freins'],
        ['Embrayage', 'embrayage'],
        ['Pneus', 'pneus'],
        ['Distribution', 'distribution'],
    ];

    public function load(ObjectManager $manager)
    {
        $key=0;
        foreach (self::COMMENTS as $value) {
            $comment = new Comment();
            $comment->setName($value[0]);
            $comment->setIdentifier($value[1]);

            $manager->persist($comment);
            $this->addReference('comment_' . $key, $comment);
            $key++;
        }
        $manager->flush();
    }
}
