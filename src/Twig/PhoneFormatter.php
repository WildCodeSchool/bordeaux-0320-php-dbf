<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PhoneFormatter extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('phoneNumber', [$this, 'formatPhoneNumber']),
        ];
    }

    private static function removeNationInPhoneNumber(string $phoneNumber): string
    {
        $searchStrings = ['+33','33(', '33 ('];
        $phoneNumber = str_replace($searchStrings, '', $phoneNumber);
        $firstNumbers = substr($phoneNumber, 0, 2);
        if ($firstNumbers === '33') {
            $phoneNumber = substr($phoneNumber, 2);
        }
        return $phoneNumber;
    }

    public static function formatPhoneNumber(string $phoneNumber): string
    {
        $searchStrings = [' ', '(', ')', '+'];
        $phoneNumber = str_replace($searchStrings, '', $phoneNumber);
        $phoneNumber =PhoneFormatter::removeNationInPhoneNumber($phoneNumber);
        if (!preg_match('/(0[0-9]{9})/', $phoneNumber)) {
            $phoneNumber = '0' . $phoneNumber;
        }
        $phoneNumber = wordwrap($phoneNumber, 2, ' ', true);
        return $phoneNumber;
    }
}
