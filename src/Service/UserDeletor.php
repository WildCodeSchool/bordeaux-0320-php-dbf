<?php


namespace App\Service;


use App\Repository\CallProcessingRepository;
use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\CityHeadRepository;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserDeletor
{

    /**
     * @var CallRepository
     */
    private CallRepository $callRepository;
    /**
     * @var CityRepository
     */
    private CityRepository $cityRepository;
    /**
     * @var ConcessionRepository
     */
    private ConcessionRepository $concessionRepository;
    /**
     * @var ServiceRepository
     */
    private ServiceRepository $serviceRepository;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var ServiceHeadRepository
     */
    private ServiceHeadRepository $serviceHeadRepository;
    /**
     * @var CityHeadRepository
     */
    private CityHeadRepository $cityHeadRepository;
    /**
     * @var ConcessionHeadRepository
     */
    private ConcessionHeadRepository $concessionHeadRepository;
    /**
     * @var CallTransferRepository
     */
    private CallTransferRepository $callTransferRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var CallProcessingRepository
     */
    private CallProcessingRepository $callProcessingRepository;

    /**
     * @var array
     */
    private array $userCalls;

    public function __construct(
        UserRepository $userRepository,
        CallRepository $callRepository,
        CityHeadRepository $cityHeadRepository,
        ConcessionHeadRepository $concessionHeadRepository,
        ServiceHeadRepository $serviceHeadRepository,
        CallTransferRepository $callTransferRepository,
        EntityManagerInterface $manager,
        CallProcessingRepository $callProcessingRepository
    )
    {
        $this->callRepository           = $callRepository;
        $this->cityHeadRepository       = $cityHeadRepository;
        $this->concessionHeadRepository = $concessionHeadRepository;
        $this->serviceHeadRepository    = $serviceHeadRepository;
        $this->userRepository           = $userRepository;
        $this->callTransferRepository   = $callTransferRepository;
        $this->manager                  = $manager;
        $this->callProcessingRepository = $callProcessingRepository;

    }

    public function processDeleting($user): void
    {
        $this->getUserCalls($user);
        $this->deleteTransfersAndProccesses($user);
        $this->deleteResponsabilities($user);
        $this->deleteCalls($user);
    }

    private function getUserCalls($user): void
    {
        $this->userCalls = $this->callRepository->findByRecipient($user);
    }


    private function deleteTransfersAndProccesses($user): void
    {
        foreach ($this->userCalls as $call) {
            if($this->callTransferRepository->findOneByReferedCall($call)) {
                $this->manager->remove($this->callTransferRepository->findOneByReferedCall($call));
            }
            if($this->callProcessingRepository->findOneByReferedCall($call)) {
                $this->manager->remove($this->callProcessingRepository->findOneByReferedCall($call));
            }
        }
        $this->manager->flush();
    }


    private function deleteCalls($user): void
    {
        $this->removeTransfers($user);
        $this->removeProcesses($user);
        $this->callRepository->removeCallsForUser($user);
        $this->callRepository->deleteAllProcessesAndTransfersWhereUserIsConcerned($user);
        $this->callRepository->removeCallsWhereUserIsAuthor($user);
        $this->callRepository->deleteRelictual();
    }

    private function removeProcesses($user)
    {
        $processes = $this->callProcessingRepository->findCallProcessingForUser($user);
        foreach ($processes as $process) {
            $this->manager->remove($process);
            $this->manager->flush();

        }
    }

    private function removeTransfers($user)
    {
        $transfers = $this->callTransferRepository->getAllTransfersForUser($user);
        foreach ($transfers as $transfer) {
            $this->manager->remove($transfer);
            $this->manager->flush();

        }
    }


    private function deleteResponsabilities($user): void
    {
        $this->cityHeadRepository->removeResponsabilitiesForUser($user);
        $this->concessionHeadRepository->removeResponsabilitiesForUser($user);
        $this->serviceHeadRepository->removeResponsabilitiesForUser($user);
    }

}
