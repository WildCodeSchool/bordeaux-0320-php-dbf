<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PhoneNumberFormatter extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('phoneNumber', [$this, 'formatPhoneNumber']),
        ];
    }

    public function formatPhoneNumber(string $phoneNumber)
    {
        $searchStrings = [' ', '+33', '(', ')'];
        $phoneNumber = str_replace($searchStrings, '', $phoneNumber);
        if (!preg_match('/(0[0-9]{9})/', $phoneNumber)) {
            $phoneNumber = '0' . $phoneNumber;
        }
        $phoneNumber = wordwrap($phoneNumber, 2, ' ', true);
        return $phoneNumber;
    }
}
