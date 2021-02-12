<?php


namespace App\Service\Landing\FormTreatment\Tools;


use App\Entity\Civility;
use App\Entity\Client;

class ClientMaker
{

    public static function make(string $name, string $phone, Civility $civility): Client
    {
        $client = new Client();
        $client->setName($name);
        $client->setPhone($phone);
        $client->setCivility($civility);

        return $client;
    }

}
