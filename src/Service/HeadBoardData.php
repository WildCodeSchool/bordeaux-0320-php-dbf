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

    const IN_PROCESS     = 'in_process';
    const TO_PROCESS     = 'to_process';
    const TO_TAKE        = 'to_take';
    const IN_PROCESS_KEY = '-in-process';
    const TO_PROCESS_KEY = '-to-process';
    const TO_TAKE_KEY    = '-to-take';

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
     * $data are provided by ServiceHeadRepository->getHeadServiceCalls()
     * The array generated by this method is like below
     * $array = [
     *      cityName => [
     *              'slug'       => city-name-slug,
     *              'in_process' => int,
     *              'to_process' => int,
     *              'concessions' => [
     *                  'concessionName' => [
     *                      'slug' => concession-name-slug,
     *                      'in_process' => int,
     *                      'to_process' => int,
     *                      'services'   => [
     *                          'serviceName' => [
     *                              'slug' => service-name-slug,
     *                              'in_process'    => int,
     *                              'to_process'    => int,
     *                              'collaborators' => [
     *                                  'userFirstName userName' => [
     *                                      'user_name'  => 'userFirstName userName',
     *                                      'to_process' => int,
     *                                      'in_process' => int,
     *                                      'user_id'    => userId
     *                                  ],
     *                              ],
     *                          ],
     *                      ],
     *                  ],
     *               ],
     *          ]
     *
     * @return array
     */

    public function makeDataForHead(array $data): array
    {
        $result = [];
        $slugify = new Slugify();
        foreach ($data as $datum) {
            $serviceId     = (int)$datum['service_id'];
            $service       = $this->serviceRepository->findOneById($serviceId);
            $concession    = $service->getConcession();
            $city          = $concession->getTown();
            $serviceName   = $service->getName();
            $collaborators = $service->getUsers();

            $result[$datum['city']]['slug'] = $slugify->slugify($datum['city']);

            $result[$datum['city']][self::IN_PROCESS] =
                $this->callRepository->countCallsInCity($city, 'in process');

            $result[$datum['city']][self::TO_PROCESS] =
                $this->callRepository->countCallsInCity($city, 'to process');


            $result[$datum['city']]['concessions'][$datum['concession']]['slug']
                = $slugify->slugify($datum['concession']);

            $result[$datum['city']]['concessions'][$datum['concession']][self::IN_PROCESS] =
                $this->callRepository->countCallsInConcession($concession, 'in process');

            $result[$datum['city']]['concessions'][$datum['concession']][self::TO_PROCESS] =
                $this->callRepository->countCallsInConcession($concession, 'to process');

            $result[$datum['city']]['concessions'][$datum['concession']]['services'][$serviceName] = [
                self::TO_PROCESS    => $this->callRepository->countCallsInService($service, 'to process'),
                self::IN_PROCESS    => $this->callRepository->countCallsInService($service, 'in process'),
                self::TO_TAKE       => $this->callRepository->countCallstoTake($service),
                'collaborators' => [],
                'slug'          => $slugify->slugify($serviceName),
            ];

            foreach ($collaborators as $collaborator) {
                $collaboratorName = $collaborator->getFirstname() . ' ' . $collaborator->getLastname();
                $result[$datum['city']]['concessions'][$datum['concession']]['services'][$serviceName]['collaborators']
                    [$collaboratorName] =
                    [
                        'user_id'    => $collaborator->getId(),
                        'slug'       => $slugify->slugify($collaboratorName),
                        'user_name'  => $collaboratorName,
                        'canBeRecipient' => $collaborator->getCanBeRecipient(),
                        self::TO_PROCESS => count($this->callRepository->callsToProcessByUser($collaborator)),
                        self::IN_PROCESS => count($this->callRepository->callsInProcessByUser($collaborator)),
                    ];

            }
        }
        return $result;
    }


    public function makeDataForHeadUpdater(array $data): array
    {
        $result = [];
        $slugify = new Slugify();
        foreach ($data as $datum) {
            $serviceId     = (int)$datum['service_id'];
            $service       = $this->serviceRepository->findOneById($serviceId);
            $concession    = $service->getConcession();
            $city = $concession->getTown();
            $serviceName   = $service->getName();
            $collaborators = $service->getUsers();

            $citySlug = $slugify->slugify($datum['city']);

            $concessionSlug    = $slugify->slugify($datum['concession']);

            $serviceSlug = $slugify->slugify($serviceName);

            //Ville
            $result[$citySlug . self::TO_PROCESS_KEY] =
                $this->callRepository->countCallsInCity($city, 'to process');

            $result[$citySlug . self::IN_PROCESS_KEY] =
                $this->callRepository->countCallsInCity($city, 'in process');

            //Concession
            $result[$citySlug . '-' . $concessionSlug . self::TO_PROCESS_KEY] =
                $this->callRepository->countCallsInConcession($concession, 'to process');

            $result[$citySlug . '-' . $concessionSlug . self::IN_PROCESS_KEY] =
                $this->callRepository->countCallsInConcession($concession, 'in process');

            //Service
            $result[$citySlug . '-' . $concessionSlug . '-' . $serviceSlug .  self::TO_PROCESS_KEY] =
                $this->callRepository->countCallsInService($service, 'to process');

            $result[$citySlug . '-' . $concessionSlug . '-' . $serviceSlug .  self::IN_PROCESS_KEY] =
                $this->callRepository->countCallsInService($service, 'in process');

            $result[$citySlug . '-' . $concessionSlug . '-' . $serviceSlug .  self::TO_TAKE_KEY] =
                $this->callRepository->countCallstoTake($service);

            foreach ($collaborators as $collaborator) {
                $collSlug =
                    $slugify->slugify($collaborator->getFirstname() . ' ' . $collaborator->getLastname());
                $result[$citySlug . '-' . $concessionSlug . '-' . $serviceSlug . '-' . $collSlug . self::TO_PROCESS_KEY]
                    = count($this->callRepository->callsToProcessByUser($collaborator));
                $result[$citySlug . '-' . $concessionSlug . '-' . $serviceSlug . '-' . $collSlug . self::IN_PROCESS_KEY]
                    = count($this->callRepository->callsInProcessByUser($collaborator));
            }
        }
        return $result;
    }
}
