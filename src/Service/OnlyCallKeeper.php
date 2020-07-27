<?php


namespace App\Service;

use App\Entity\Call;

class OnlyCallKeeper
{
    public static function keepCalls(array $data)
    {
        $calls = [];
        foreach ($data as $datum) {
            if ($datum instanceof Call) {
                $calls[]= $datum;
            }
        }
        return $calls;
    }
}
