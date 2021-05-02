<?php


namespace App\Service\Deletors;


use App\Entity\Concession;
use App\Repository\CallProcessingRepository;
use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConcessionDeletor extends ServiceDeletor
{

    /**
     * @var Concession
     */
    private Concession $concession;

    public function __construct(ConcessionHeadRepository $concessionHeadRepository, ServiceHeadRepository $serviceHeadRepository, CallRepository $callRepository, CallProcessingRepository $callProcessingRepository, CallTransferRepository $callTransferRepository, ServiceRepository $serviceRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($concessionHeadRepository, $serviceHeadRepository, $callRepository, $callProcessingRepository, $callTransferRepository, $serviceRepository, $entityManager);
    }

    public function deleteConcession(Concession $concession)
    {
        $this->concession = $concession;

        $this->removeResponsabilities();

        $services = $concession->getServices();

        foreach ($services as $service)
        {
            $this->deleteService($service);
        }

        $this->entityManager->remove($concession);
        $this->entityManager->flush();

    }

    private function removeResponsabilities()
    {
        $this->concessionHeadRepository->removeAllResponsabilitiesInConcession($this->concession);

    }

}
