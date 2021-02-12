<?php


namespace App\Service\Landing\FormTreatment\Tools;


use App\Entity\Subject;

class SubjectMaker
{
    public static function make(string $name, int $isAppForWorkshop, int $isHidden, City $city): Subject
    {
        $subject = new Subject();
        $subject->setName($name)
            ->setIsForAppWorkshop($isAppForWorkshop)
            ->setIsHidden($isHidden)
            ->setCity($city);
        return $subject;
    }

}
