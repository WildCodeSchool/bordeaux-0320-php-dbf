<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PhoneFormatter extends AbstractExtension
{
    const FRANCE = '33';

    public function getFilters()
    {
        return [
            new TwigFilter('phoneNumber', [$this, 'formatPhoneNumber']),
        ];
    }

    protected static function removeNationInPhoneNumber(string $phoneNumber): string
    {
        $searchStrings = ['+' . self::FRANCE, self::FRANCE . '(', self::FRANCE . ' ('];
        $phoneNumber = str_replace($searchStrings, '', $phoneNumber);
        $firstNumbers = substr($phoneNumber, 0, 2);
        if ($firstNumbers === self::FRANCE) {
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
        return wordwrap($phoneNumber, 2, ' ', true);
    }
}
