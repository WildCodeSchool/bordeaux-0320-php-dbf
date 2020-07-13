<?php


namespace App\Service;

use App\Repository\CallRepository;
use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;

/**
 * Class HeadBoardData
 * @package App\Service
 */
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

    /**
     * @param array $data
     * $data is provided by ServiceHeadRepository->getHeadServiceCalls()
     * @return array
     */

    public function makeDataForHead(array $data): array
    {
        $result = [];
        $slugify = new Slugify();
        foreach ($data as $datum) {
            $serviceId     = (int)$datum['service_id'];
            $service       = $this->serviceRepository->findOneById($serviceId);
            $serviceName   = $service->getName();
            $collaborators = $service->getUsers();
            $result[$datum['city']]['slug'] = $slugify->slugify($datum['city']);
            $result[$datum['city']]['in_process'] = (!isset($result[$datum['city']]['in_process']))
                ? 0 : $result[$datum['city']]['in_process'];
            $result[$datum['city']]['to_process'] = (!isset($result[$datum['city']]['to_process']))
                ? 0 : $result[$datum['city']]['to_process'];
            $result[$datum['city']]['in_process'] += (is_null($datum['inprocess'])) ? 0 : $datum['inprocess'];
            $result[$datum['city']]['to_process'] += (is_null($datum['toprocess'])) ? 0 : $datum['toprocess'];

            $result[$datum['city']]['concessions'][$datum['concession']]['slug']
                = $slugify->slugify($datum['concession']);
            $result[$datum['city']]['concessions'][$datum['concession']]['in_process'] = (!isset($result[$datum['city']]
                ['concessions'][$datum['concession']]['in_process'])) ? 0 : $result[$datum['city']]['in_process'];
            $result[$datum['city']]['concessions'][$datum['concession']]['to_process'] = (!isset($result[$datum['city']]
                ['concessions'][$datum['concession']]['to_process'])) ? 0 : $result[$datum['city']]['to_process'];
            $result[$datum['city']]['concessions'][$datum['concession']]['in_process'] += (is_null($datum['inprocess']))
                ? 0 : $datum['inprocess'];
            $result[$datum['city']]['concessions'][$datum['concession']]['to_process'] += (is_null($datum['toprocess']))
                ? 0 : $datum['toprocess'];

            $result[$datum['city']]['concessions'][$datum['concession']]['services'][$serviceName] = [
                'to_process'    => (is_null($datum['toprocess'])) ? 0 : $datum['toprocess'],
                'in_process'    => (is_null($datum['inprocess'])) ? 0 : $datum['inprocess'],
                'collaborators' => [],
                'slug'          => $slugify->slugify($serviceName),
            ];
            foreach ($collaborators as $collaborator) {
                $result[$datum['city']]['concessions'][$datum['concession']]['services'][$serviceName]['collaborators']
                    [$collaborator->getFirstname() . ' ' . $collaborator->getLastname()] =
                    [
                        'user_id'   => $collaborator->getId(),
                        'user_name' => $collaborator->getFirstname() . ' ' . $collaborator->getLastname(),
                        'to_process' => count($this->callRepository->callsToProcessByUser($collaborator)),
                        'in_process' => count($this->callRepository->callsInProcessByUser($collaborator)),
                    ];
            }
        }
        return $result;
    }
}
