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
            $vehicles = $client->getVehicles();
            $data = [
                'client' => [
                    'client_id'       => $client->getId(),
                    'client_name'     => $client->getName(),
                    'client_civility' => $client->getCivility()->getName(),
                    'client_phone'    => $client->getPhone(),
                    'client_phone2'   => $client->getPhone2(),
                    'client_email'    => $client->getEmail(),
                    'vehicles'        => [],
                ],
            ];
            if (!empty($vehicles)) {
                foreach ($vehicles as $vehicle) {
                    $data['client']['vehicles'][] = [
                        'vehicle_id'              => $vehicle->getId(),
                        'vehicle_immatriculation' => $vehicle->getImmatriculation(),
                        'vehicle_chassis'         => $vehicle->getChassis(),
                        'vehicle_hasCome'         => $vehicle->getHasCome(),
                    ];
                }
            }
        }
        if ($calls) {
            $data['calls'] = [];
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
