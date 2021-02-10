<?php


namespace App\Command;


use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddServiceBrandCommand extends Command
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * @var ServiceRepository
     */
    private ServiceRepository $serviceRepository;

    public function __construct(EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        parent::__construct();
        $this->manager = $entityManager;
        $this->serviceRepository = $serviceRepository;

    }

    protected function configure()
    {
        $this->setName('services:addbrand');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $services = $this->serviceRepository->findAll();

        foreach ($services as $service) {
            $concessionBrand = $service->getConcession()->getBrand();
            if (null !== $concessionBrand && '' !== $concessionBrand) {
                $service->setBrand($concessionBrand);

                if ('Audi' !== $concessionBrand && 'Volkswagen' !== $concessionBrand) {
                    $service->setBrand('Audi');
                }
                $this->manager->persist($service);
            }
        }
        $this->manager->flush();

        $io->writeln('operation done');
    }
}
