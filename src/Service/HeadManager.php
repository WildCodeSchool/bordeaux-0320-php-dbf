<?php


namespace App\Service;


use App\Entity\Concession;
use App\Entity\ConcessionHead;
use App\Entity\Service;
use App\Entity\ServiceHead;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ServiceHeadRepository;
use Doctrine\ORM\EntityManagerInterface;

class HeadManager
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var ServiceHeadRepository
     */
    private ServiceHeadRepository $serviceHeadRepository;
    /**
     * @var ConcessionHeadRepository
     */
    private ConcessionHeadRepository $concessionHeadRepository;

    public function __construct(EntityManagerInterface $manager,  ServiceHeadRepository $serviceHeadRepository, ConcessionHeadRepository $concessionHeadRepository)
    {
        $this->manager = $manager;
        $this->serviceHeadRepository = $serviceHeadRepository;
        $this->concessionHeadRepository = $concessionHeadRepository;
    }

    public function addServiceHeads(Service $service)
    {
        $concession      = $service->getConcession();
        $concessionHeads = $concession->getConcessionHeads();

        foreach ($concessionHeads as $concessionHead)
        {
            if(!$this->serviceHeadRepository->isServiceHead($concessionHead->getUser(), $service)) {
                $serviceHead = new ServiceHead();
                $serviceHead->setUser($concessionHead->getUser());
                $serviceHead->setService($service);
                $this->manager->persist($serviceHead);
            }
        }
        $this->manager->flush();
    }

    public function addConcessionHeads(Concession $concession)
    {
        $city      = $concession->getTown();
        $cityHeads = $city->getCityHeads();

        foreach ($cityHeads as $cityHead)
        {
            if(!$this->concessionHeadRepository->isConcessionHead($cityHead->getUser(), $concession)) {
                $concessionHead = new ConcessionHead();
                $concessionHead->setUser($cityHead->getUser());
                $concessionHead->setConcession($concession);
                $this->manager->persist($concessionHead);
            }
        }
        $this->manager->flush();
    }

}
