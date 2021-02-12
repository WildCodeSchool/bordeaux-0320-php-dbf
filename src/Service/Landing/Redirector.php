<?php


namespace App\Service\Landing;


class Redirector
{
    const BASE_URL = 'https://www.';

    const BASE_EXT = 'fr';

    public static function makeUrl(string $cityName, string $brand)
    {
        $ext = ucfirst($cityName) === 'Toulouse' ? 'com' : self::BASE_EXT;

        return self::BASE_URL . strtolower($brand) . '-' . strtolower($cityName) . '.' . $ext;

    }

}
