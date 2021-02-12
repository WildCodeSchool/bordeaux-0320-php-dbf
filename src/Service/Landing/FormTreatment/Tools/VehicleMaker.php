<?php


namespace App\Service\Landing\FormTreatment\Tools;


use App\Entity\Client;
use App\Entity\Vehicle;

class VehicleMaker
{

    public static function make(Client $client, string $immat): Vehicle
    {
        $vehicle = new Vehicle();
        $vehicle->setClient($client);
        $vehicle->setImmatriculation($immat);
        $vehicle->setHasCome(0);
        return $vehicle;
    }

}
