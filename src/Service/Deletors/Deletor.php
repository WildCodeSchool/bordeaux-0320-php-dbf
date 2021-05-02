<?php


namespace App\Service\Deletors;


use App\Repository\CallProcessingRepository;
use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class Deletor
{

    public function __construct(
        ConcessionHeadRepository $concessionHeadRepository,
        ServiceHeadRepository $serviceHeadRepository,
        CallRepository $callRepository,
        CallProcessingRepository $callProcessingRepository,
        CallTransferRepository $callTransferRepository,
        ServiceRepository $serviceRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->callProcessingRepository = $callProcessingRepository;
        $this->entityManager = $entityManager;
        $this->callTransferRepository = $callTransferRepository;
        $this->concessionHeadRepository = $concessionHeadRepository;
        $this->serviceHeadRepository = $serviceHeadRepository;
        $this->callRepository = $callRepository;
        $this->serviceRepository = $serviceRepository;
    }





}
