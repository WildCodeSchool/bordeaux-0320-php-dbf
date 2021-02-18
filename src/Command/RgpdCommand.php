<?php


namespace App\Command;


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
use Symfony\Component\Mailer\MailerInterface;

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
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        CallRepository $callRepository,
        CallTransferRepository $callTransferRepository,
        VehicleRepository $vehicleRepository,
        ClientRepository $clientRepository
    ) {
        $this->manager = $entityManager;
        $this->callRepository = $callRepository;
        $this->callTransferRepository = $callTransferRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->clientRepository = $clientRepository;
        //$this->mailer = $mailer;
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
            foreach ($clients as $client) {
                $client = $this->clientRepository->findOneById($client['id']);
                $this->manager->remove($client);
            }
            $this->manager->flush();
            $nbClients = count($clients);
            $io->writeln($nbClients . ' clients supprimés');
            $results['clients'] = $nbClients;

        $email = (new TemplatedEmail())
            ->from($_SERVER['MAILER_FROM_ADDRESS'])
            ->to($_SERVER['REPORT_DESTINATARY'])
            ->subject('Rapport de maintenance RGPD de la base Easy Auto')
            ->htmlTemplate('emails/template_rgpd.html.twig')
            ->context([
                'date' => $today,
                'data' => $results,
                'ref'  => $date
            ]);
        //$this->mailer->send($email);

        return self::SUCCESS;
    }

}
