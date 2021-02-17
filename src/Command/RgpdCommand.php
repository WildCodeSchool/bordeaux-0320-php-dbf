<?php


namespace App\Command;


use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\ClientRepository;
use App\Repository\ServiceRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RgpdCommand extends Command
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var CallRepository
     */
    private CallRepository $callRepository;
    /**
     * @var CallTransferRepository
     */
    private CallTransferRepository $callTransferRepository;
    /**
     * @var VehicleRepository
     */
    private VehicleRepository $vehicleRepository;

    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CallRepository $callRepository,
        CallTransferRepository $callTransferRepository,
        VehicleRepository $vehicleRepository,
        ClientRepository $clientRepository
    ) {
        parent::__construct();
        $this->manager = $entityManager;
        $this->callRepository = $callRepository;
        $this->callTransferRepository = $callTransferRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->clientRepository = $clientRepository;
    }

    protected function configure()
    {
        $this->setName('maintenance:rgpd');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $date = new \DateTime('now');
        $date->sub(new \DateInterval('P3Y'))->sub(new \DateInterval('P7D'));


        $io->title('Suppression des appels de plus de 36 mois');
            $io->writeln($this->callRepository->removeOldCalls($date) . ' appels supprimés');

        $io->title('suppression des historiques de transfert de plus plus de 36 mois');
            $io->writeln($this->callTransferRepository->removeOldCalls($date) . ' transferts supprimés');

        $io->title('Récupération des véhicules qui ne sont plus dans la table des appels');
            $vehicles = $this->vehicleRepository->getOldVehicles();
            foreach ($vehicles as $vehicle) {
                $vehicle = $this->vehicleRepository->findOneById($vehicle['id']);
                $this->manager->remove($vehicle);
            }
            $this->manager->flush();
            $io->writeln(count($vehicles) . ' véhicules supprimés');

        $io->title('Suppression des clients qui n\'ont plus aucun appel les concernant');
            $clients = $this->clientRepository->getOldClients();
            foreach ($clients as $client) {
                $client = $this->clientRepository->findOneById($client['id']);
                $this->manager->remove($client);
            }
            $this->manager->flush();
            $io->writeln(count($clients) . ' clients supprimés');
    }

}
