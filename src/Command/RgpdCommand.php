<?php


namespace App\Command;


use App\Command\Service\RgpdCommandMailer;
use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\ClientRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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

    /**
     * @var RgpdCommandMailer
     */
    private RgpdCommandMailer $mailer;


    public function __construct(
        EntityManagerInterface $entityManager,
        CallRepository $callRepository,
        CallTransferRepository $callTransferRepository,
        VehicleRepository $vehicleRepository,
        ClientRepository $clientRepository,
        RgpdCommandMailer $mailer
    ) {
        $this->manager = $entityManager;
        $this->callRepository = $callRepository;
        $this->callTransferRepository = $callTransferRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->clientRepository = $clientRepository;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('maintenance:rgpd');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $results = [];

        $date  = new \DateTime('now');
        $today = clone $date;

        $date->sub(new \DateInterval('P3Y'))->sub(new \DateInterval('P7D'));


        $io->title('Suppression des appels de plus de 36 mois');
        $nbCalls = $this->callRepository->removeOldCalls($date);
            $io->writeln($nbCalls . ' appels supprimés');
            $results['calls'] = $nbCalls;

            $io->title('suppression des historiques de transfert de plus plus de 36 mois');
            $nbTransfers = $this->callTransferRepository->removeOldCalls($date);
            $io->writeln($nbTransfers . ' transferts supprimés');
            $results['transfers'] = $nbTransfers;

        $io->title('Récupération des véhicules qui ne sont plus dans la table des appels');
            $vehicles = $this->vehicleRepository->getOldVehicles();
            foreach ($vehicles as $vehicle) {
                $vehicle = $this->vehicleRepository->findOneById($vehicle['id']);
                $this->manager->remove($vehicle);
            }
            $this->manager->flush();
            $nbVehicles = count($vehicles);
            $io->writeln($nbVehicles . ' véhicules supprimés');
            $results['vehicles'] = $nbVehicles;

        $io->title('Suppression des clients qui n\'ont plus aucun appel les concernant');
            $clients = $this->clientRepository->getOldClients();
            $loopIndex = 0;
            foreach ($clients as $client) {
                $loopIndex++;
                $client = $this->clientRepository->findOneById($client['id']);
                $this->deleteVehiculesForClient($client);
                if($this->callRepository->findBy([
                        'client' => $client
                    ]) === null) {
                    $this->manager->remove($client);
                }
                if($loopIndex%100 === 0) {
                    $this->manager->flush();
                }
            }

            $this->manager->flush();
            $nbClients = count($clients);
            $io->writeln($nbClients . ' clients supprimés');
            $results['clients'] = $nbClients;

        $this->mailer->send($results, $date);

        return self::SUCCESS;
    }

    private function deleteVehiculesForClient($client)
    {
        $vehicles = $this->vehicleRepository->findBy([
            'client' => $client
        ]);
        if ($vehicles) {
            foreach ($vehicles as $vehicle) {
                if($this->callRepository->findBy([
                    'vehicle' => $vehicle
                ]) === null) {
                    $this->manager->remove($vehicle);
                }
            }
            $this->manager->flush();
        }
    }

}
