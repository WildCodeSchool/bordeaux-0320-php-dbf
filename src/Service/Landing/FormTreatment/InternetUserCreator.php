<?php


namespace App\Service\Landing\FormTreatment;


use App\Entity\User;
use App\Repository\CivilityRepository;
use App\Repository\ServiceRepository;

class InternetUserCreator
{
    /**
     * @var CivilityRepository
     */
    private CivilityRepository $civilityRepository;
    /**
     * @var ServiceRepository
     */
    private ServiceRepository $serviceRepository;

    public function __construct(CivilityRepository $civilityRepository, ServiceRepository $serviceRepository)
    {
        $this->civilityRepository = $civilityRepository;
        $this->serviceRepository  = $serviceRepository;

    }

    public function create()
    {
        $author = new User();
        $author->setFirstname('INTERNET')
            ->setLastname('DBF AUTOS')
            ->setEmail('easyauto@dbf-autos.fr')
            ->setCanBeRecipient(0)
            ->setPassword('1234')
            ->setRoles(['ROLE_COLLABORATOR'])
            ->setCivility($this->civilityRepository->findOneByName('M.'))
            ->setPhone('0808080808')
            ->setHasAcceptedAlert(0)
            ->setService($this->serviceRepository->findOneByName('Cellule téléphonique'))
        ;
        return $author;
    }

}
