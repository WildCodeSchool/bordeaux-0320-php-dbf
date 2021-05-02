<?php


namespace App\Service\Deletors;


use App\Repository\CallProcessingRepository;
use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Service;
use App\Entity\User;

class ServiceDeletor extends Deletor
{
    /**
     * @var User[]|\Doctrine\Common\Collections\Collection
     */
    private $users;
    /**
     * @var Service
     */
    private Service $service;

    public function __construct(ConcessionHeadRepository $concessionHeadRepository, ServiceHeadRepository $serviceHeadRepository, CallRepository $callRepository, CallProcessingRepository $callProcessingRepository, CallTransferRepository $callTransferRepository, ServiceRepository $serviceRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($concessionHeadRepository, $serviceHeadRepository, $callRepository, $callProcessingRepository, $callTransferRepository, $serviceRepository, $entityManager);

    }

    public function deleteService(Service $service)
    {
        $this->service = $service;
        $this->users = $service->getUsers();

        $this->deleteCallsForService();
        $this->removeResponsabilities();
        $this->removeAllElementsForUsersInService();
        $this->removeService();
    }

    private function deleteCallsForService()
    {
        $this->callRepository->removeCallsForService($this->service);
    }

    private function removeResponsabilities()
    {
        $this->serviceHeadRepository->removeAllResponsabilitiesForService($this->service);
    }

    private function removeProcesses($user)
    {
        $processes = $this->callProcessingRepository->findCallProcessingForUser($user);
        foreach ($processes as $process) {
            $this->entityManager->remove($process);
            $this->entityManager->flush();

        }
    }

    private function removeTransfers($user)
    {
        $transfers = $this->callTransferRepository->getAllTransfersForUser($user);
        foreach ($transfers as $transfer) {
            $this->entityManager->remove($transfer);
            $this->entityManager->flush();

        }
    }

    private function removeCallsForUser($user)
    {
        $this->callRepository->removeCallsForUser($user);
        $this->callRepository->deleteAllProcessesAndTransfersWhereUserIsConcerned($user);
        $this->callRepository->removeCallsWhereUserIsAuthor($user);
        $this->callRepository->deleteRelictual();
    }

    private function removeServiceForUser($user)
    {
        if(!$user->isAdmin()) {
            $user->setService(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    private function removeAllElementsForUsersInService()
    {
        foreach ($this->users as $user) {
            $this->removeProcesses($user);
            $this->removeTransfers($user);
            $this->removeCallsForUser($user);
            $this->removeServiceForUser($user);
        }

    }

    private function removeService()
    {
        $this->entityManager->remove($this->service);
        $this->entityManager->flush();
    }


}
