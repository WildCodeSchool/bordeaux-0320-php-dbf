<?php


namespace App\Service\Landing;


class OriginChecker
{
    const ORIGIN_TO_CHECK = 'blog.frvaillant.fr';
    const FIRST_KEY = 'Ety67D@dBf!';
    const SECOND_KEY = 'rT6-re';


    public static function isValidOrigin($data)
    {
        list($hash, $referer) = explode(':::', $data);
        if (!$referer || null === $referer) {
            return false;
        }
        $refererParams = parse_url($referer);
        if (password_verify(self::FIRST_KEY . $refererParams['host'] . self::SECOND_KEY, $hash)) {
            return true;
        }
        return false;
    }

    public static function encryptReferer()
    {
        return password_hash(self::FIRST_KEY . self::ORIGIN_TO_CHECK . self::SECOND_KEY, PASSWORD_ARGON2ID);
    }

}
