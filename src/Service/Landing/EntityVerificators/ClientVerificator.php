<?php


namespace App\Service\Landing\EntityVerificators;


use App\Repository\ClientRepository;
use App\Entity\Client;

class ClientVerificator
{
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function checkClient($name, $phoneNumber): ?Client
    {
        $phoneNumber = str_replace(' ', '', $phoneNumber);

        $clients = $this->clientRepository->findBy([
            'name' => $name
        ]);

        foreach ($clients as $client) {
            if(
                str_replace(' ', '', $client->getPhone()) === $phoneNumber ||
                str_replace(' ', '', $client->getPhone2()) === $phoneNumber
            ) {
                return $client;
            }
        }
        return null;
    }
}
