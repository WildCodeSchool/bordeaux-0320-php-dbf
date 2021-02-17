<?php


namespace App\Command;


use App\Events;
use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\ClientRepository;
use App\Repository\ServiceRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Bundle\SecurityBundle\Tests\Functional\app\AppKernel;
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
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        CallRepository $callRepository,
        CallTransferRepository $callTransferRepository,
        VehicleRepository $vehicleRepository,
        ClientRepository $clientRepository,
        EventDispatcherInterface $eventDispatcher,
        MailerInterface $mailer
    ) {
        parent::__construct();
        $this->manager = $entityManager;
        $this->callRepository = $callRepository;
        $this->callTransferRepository = $callTransferRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->clientRepository = $clientRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setName('maintenance:rgpd');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $results = [];

        $date = new \DateTime('now');
        $date->sub(new \DateInterval('P3Y'))->sub(new \DateInterval('P7D'));

        $io->title('Suppression des appels de plus de 36 mois');
            $calls = $this->callRepository->removeOldCalls($date);
            $results['calls'] = $calls;
            $io->writeln($calls . ' appels supprimés');

        $io->title('suppression des historiques de transfert de plus plus de 36 mois');
            $transfers = $this->callTransferRepository->removeOldCalls($date);
            $results['transfers'] = $transfers;
            $io->writeln($transfers . ' transferts supprimés');

        $io->title('Récupération des véhicules qui ne sont plus dans la table des appels');
            $vehicles = $this->vehicleRepository->getOldVehicles();
            foreach ($vehicles as $vehicle) {
                $vehicle = $this->vehicleRepository->findOneById($vehicle['id']);
                $this->manager->remove($vehicle);
            }

            $this->manager->flush();
            $results['vehicles'] = count($vehicles);
            $io->writeln(count($vehicles) . ' véhicules supprimés');

        $io->title('Suppression des clients qui n\'ont plus aucun appel les concernant');
            $clients = $this->clientRepository->getOldClients();
            foreach ($clients as $client) {
                $client = $this->clientRepository->findOneById($client['id']);
                $this->manager->remove($client);
            }
            $this->manager->flush();
            $results['clients'] = count($clients);
            $io->writeln(count($clients) . ' clients supprimés');

            $dateExec = new \DateTime('now');

        $email = (new TemplatedEmail())
            ->from($_SERVER['MAILER_FROM_ADDRESS'])
            ->to($_SERVER['REPORT_DESTINATARY'])
            ->subject('Compte rendu RGPD BackUp')
            ->htmlTemplate('emails/template_rgpd_mail.html.twig')
            ->context([
                'data'     => $results,
                'dateexec' => $dateExec,
                'refdate'  => $date
            ]);

        $this->mailer->send($email);
    }

}
