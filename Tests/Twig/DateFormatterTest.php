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

        $this->assertEquals('aujourd\'hui', dateFormatter::formatDate($today));
        $this->assertEquals('hier', dateFormatter::formatDate($yesterday));
        $this->assertEquals('demain', dateFormatter::formatDate($tomorrow));
        $this->assertEquals('le 14/05/2020', dateFormatter::formatDate($oneDay));
    }


    public function testFormatTime()
    {
        $time1 = new DateTime('2020-05-14T14:00:00');
        $time2 = new DateTime('2020-05-14T14:27:00');

        $this->assertEquals('14h', dateFormatter::formatTime($time1));
        $this->assertEquals('14h27', dateFormatter::formatTime($time2));
    }
}
