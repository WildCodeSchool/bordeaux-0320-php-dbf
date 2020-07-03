<?php

namespace App\Tests\Twig;

use App\Twig\DateFormatter;
use PHPUnit\Framework\TestCase;
use \DateTime;
use \DateInterval;

class DateFormatterTest extends testCase
{
    public function testFormatDate()
    {
        $today     = new DateTime('now');
        $yesterday = new DateTime('now');
        $yesterday->sub(new DateInterval('P1D'));
        $tomorrow  = new DateTime('now');
        $tomorrow->add(new DateInterval('P1D'));

        $oneDay = new DateTime('2020-05-14T00:00:00');

        $dateFormatter = new DateFormatter();
        $resToday      = $dateFormatter::formatDate($today);
        $resTomorrow   = $dateFormatter::formatDate($tomorrow);
        $resYesterday  = $dateFormatter::formatDate($yesterday);
        $resOneDay     = $dateFormatter::formatDate($oneDay);

        $this->assertEquals('aujourd\'hui', $resToday);
        $this->assertEquals('hier', $resYesterday);
        $this->assertEquals('demain', $resTomorrow);
        $this->assertEquals('le 14/05/2020', $resOneDay);
    }


    public function testFormatTime()
    {
        $time1 = new DateTime('2020-05-14T14:00:00');
        $time2 = new DateTime('2020-05-14T14:27:00');

        $this->assertEquals('14h', dateFormatter::formatTime($time1));
        $this->assertEquals('14h27', dateFormatter::formatTime($time2));
    }
}
