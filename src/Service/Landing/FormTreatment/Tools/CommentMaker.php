<?php


namespace App\Service\Landing\FormTreatment\Tools;


use App\Entity\Comment;

class CommentMaker
{
    public static function make(string $name, int $isHidden, ?string $identifier = null): Comment
    {
        $comment = new Comment();
        $comment->setName($name)
            ->setIsHidden($isHidden)
            ->setIdentifier($identifier)
        ;
        return $comment;
    }

}
