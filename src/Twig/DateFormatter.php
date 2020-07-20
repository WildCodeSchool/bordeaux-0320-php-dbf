<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use \DateTime;
use \DateInterval;

class DateFormatter extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('dateName', [$this, 'formatDate']),
            new TwigFilter('timeFormat', [$this, 'formatTime']),
        ];
    }

    public static function formatDate(DateTime $dateTime): string
    {
        $today     = new DateTime('now');
        $yesterday = new DateTime('now');
        $yesterday->sub(new DateInterval('P1D'));
        $tomorrow  = new DateTime('now');
        $tomorrow->add(new DateInterval('P1D'));

        $dateName  = 'le ' . $dateTime->format('d/m/Y');

        if ($dateTime->format('d-m-Y') === $yesterday->format('d-m-Y')) {
            $dateName = 'hier';
        }
        if ($dateTime->format('d-m-Y') === $today->format('d-m-Y')) {
            $dateName = 'aujourd\'hui';
        }
        if ($dateTime->format('d-m-Y') === $tomorrow->format('d-m-Y')) {
            $dateName = 'demain';
        }
        return $dateName;
    }
    public static function formatTime(DateTime $dateTime): string
    {
        $minutes = ($dateTime->format('i') === '00') ? '' : $dateTime->format('i');
        return $dateTime->format('H') . 'h'. $minutes;
    }
}
