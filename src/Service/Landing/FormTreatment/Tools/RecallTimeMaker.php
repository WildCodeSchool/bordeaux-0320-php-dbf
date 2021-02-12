<?php


namespace App\Service\Landing\FormTreatment\Tools;


class RecallTimeMaker
{

    public static function makeTime($event): string
    {
        $hour = $event->getSubject()->get('callHour')->getData() < 10 ? '0' . $event->getSubject()->get('callHour')->getData() : $event->getSubject()->get('callHour')->getData();
        $minutes = $event->getSubject()->get('callMinutes')->getData() < 10 ? '0' . $event->getSubject()->get('callMinutes')->getData() : $event->getSubject()->get('callMinutes')->getData();
        return $hour . ':' . $minutes . ':00';
    }

}
