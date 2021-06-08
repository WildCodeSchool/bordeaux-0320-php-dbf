<?php


namespace App\Service;


use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;

class PhoneCityCallErrorPrevent
{

    private $cityRepository;
    private $concessionRepository;
    private $userRepository;
    private $serviceRepository;

    private $data;

    public function __construct(
        CityRepository $cityRepository,
        ConcessionRepository $concessionRepository,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository
    ) {
          $this->userRepository = $userRepository;
          $this->cityRepository = $cityRepository;
          $this->concessionRepository = $concessionRepository;
          $this->serviceRepository = $serviceRepository;
    }


    public function isRecipientCoherent($data)
    {
        $this->data = $data;
        if(isset($data['city']) && isset($data['concession'])) {
            return (
                $this->getRecipient()->getService() === $this->getService() &&
                $this->getService()->getConcession() === $this->getConcession() &&
                $this->getConcession()->getTown() === $this->getCity()
            );
        }

    }

    private function getCity()
    {
        return $this->cityRepository->findOneById($this->data['city']);

    }

    private function getConcession()
    {
        return $this->concessionRepository->findOneById($this->data['concession']);

    }

    private function getService()
    {
        return $this->serviceRepository->findOneById($this->data['service']);

    }

    private function getRecipient()
    {
        return $this->userRepository->findOneById($this->data['recipient']);
    }


}
