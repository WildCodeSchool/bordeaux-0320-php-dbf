<?php

namespace App\Tests\Twig;

use App\Twig\PhoneFormatter;
use PHPUnit\Framework\TestCase;

class PhoneFormatterTest extends testCase
{
    public function testFormatPhoneNumber()
    {
        $phoneNumber = '05 56 45 67 54';
        $this->assertEquals('05 56 45 67 54', PhoneFormatter::formatPhoneNumber($phoneNumber));

        $phoneNumber = '0556456754';
        $this->assertEquals('05 56 45 67 54', PhoneFormatter::formatPhoneNumber($phoneNumber));

        $phoneNumber = '+33(0)5 56456754';
        $this->assertEquals('05 56 45 67 54', PhoneFormatter::formatPhoneNumber($phoneNumber));

        $phoneNumber = '+33 05 56 45 67 54';
        $this->assertEquals('05 56 45 67 54', PhoneFormatter::formatPhoneNumber($phoneNumber));

        $phoneNumber = '+33 (0)5 56 45 67 54';
        $this->assertEquals('05 56 45 67 54', PhoneFormatter::formatPhoneNumber($phoneNumber));

        $phoneNumber = ' 5 56 45 6754';
        $this->assertEquals('05 56 45 67 54', PhoneFormatter::formatPhoneNumber($phoneNumber));

        $phoneNumber = '33 5 56 45 6754';
        $this->assertEquals('05 56 45 67 54', PhoneFormatter::formatPhoneNumber($phoneNumber));
    }
}
