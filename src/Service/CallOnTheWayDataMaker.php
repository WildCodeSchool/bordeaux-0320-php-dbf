<?php


namespace App\Service;

use App\Entity\Client;

class CallOnTheWayDataMaker
{
    public function arrayMaker(?Client $client, ?Array $calls)
    {
        $data = [
            'client' => [
                'client_id' => null
             ]
        ];
        if ($client) {
            $data = [
                'client' => [
                    'client_id'       => $client->getId(),
                    'client_name'     => $client->getName(),
                    'client_civility' => $client->getCivility()->getName(),
                    'client_phone'    => $client->getPhone(),
                ],
                'calls' => []
            ];
        }
        if ($calls) {
            foreach ($calls as $call) {
                $data['calls'][] = [
                    'call_id'       => $call->getId(),
                    'call_subject'  => $call->getSubject()->getName(),
                    'call_comment'  => $call->getComment()->getName(),
                    'call_date'     => $call->getCreatedAt()->format('d-m-Y'),
                    'call_hour'     => $call->getCreatedAt()->format('H:i'),
                    'call_vehicule' => $call->getVehicle()->getImmatriculation(),
                ];
            }
        }
        return $data;
    }
}
