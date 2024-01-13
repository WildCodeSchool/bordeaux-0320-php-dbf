<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use \DateTime;
use \DateInterval;

class DateFormatter extends AbstractExtension
{
    const DATE_FORMAT = 'd-m-Y';

    public function getFilters()
    {
        return [
            new TwigFilter('dateName', [$this, 'formatDate']),
            new TwigFilter('timeFormat', [$this, 'formatTime']),
        ];
    }

    public static function formatDate(DateTime $dateTime, $isSmall = false): string
    {
        $today     = new DateTime('now');
        $yesterday = new DateTime('now');
        $yesterday->sub(new DateInterval('P1D'));
        $tomorrow  = new DateTime('now');
        $tomorrow->add(new DateInterval('P1D'));

        $dateName  = $isSmall ? $dateTime->format('d/m/y') : 'le ' . $dateTime->format('d/m/Y');

        if ($dateTime->format(self::DATE_FORMAT) === $yesterday->format(self::DATE_FORMAT)) {
            $dateName = 'hier';
        }
        if ($dateTime->format(self::DATE_FORMAT) === $today->format(self::DATE_FORMAT)) {
            $dateName = 'aujourd\'hui';
        }
        if ($dateTime->format(self::DATE_FORMAT) === $tomorrow->format(self::DATE_FORMAT)) {
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
