<?php


namespace App\Service;

use App\Repository\CallRepository;
use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;

class HeadBoardData
{
    private $callRepository;
    private $cityRepository;
    private $concessionRepository;
    private $serviceRepository;
    private $userRepository;
    private $serviceHeadRepository;

    public function __construct(
        CallRepository $callRepository,
        CityRepository $cityRepository,
        ConcessionRepository $concessionRepository,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository,
        ServiceHeadRepository $serviceHeadRepository
    ) {
        $this->callRepository        = $callRepository;
        $this->cityRepository        = $cityRepository;
        $this->concessionRepository  = $concessionRepository;
        $this->serviceRepository     = $serviceRepository;
        $this->userRepository        = $userRepository;
        $this->serviceHeadRepository = $serviceHeadRepository;
    }

    public function makeDataForHeads($servicesHead)
    {
        $responseData =[];

        foreach ($servicesHead as $serviceHead) {
            $service = $serviceHead->getService();
            $totalNotProcessingCalls = $this->callRepository->getNotInProcessCallsByService($service);
            $totalProcessingCalls    = $this->callRepository->getInProcessCallsByService($service);

            $users = $service->getUsers();
            $usersCalls = [];
            foreach ($users as $user) {
                $usersCalls[] = [
                    'name'       => $user->getFirstName() . ' ' . $user->getLastName(),
                    'to_process' => count($this->callRepository->callsToProcessByUser($user)),
                    'in_process' => count($this->callRepository->callsInProcessByUser($user))
                ];
            }

            $responseData[] = [
                'name'          => $service->getName(),
                'not_processed' => $totalNotProcessingCalls,
                'processed'     => $totalProcessingCalls,
                'users'         => $usersCalls
            ];
        }
        return $responseData;
    }
}
