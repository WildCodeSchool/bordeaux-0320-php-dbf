<?php


namespace App\Service\Landing\FormTreatment;


use App\Entity\Call;
use App\Entity\Comment;
use App\Entity\Client;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Events;
use App\Repository\CityRepository;
use App\Repository\CivilityRepository;
use App\Repository\CommentRepository;
use App\Repository\RecallPeriodRepository;
use App\Repository\ServiceRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use App\Service\Landing\EntityVerificators\ClientVerificator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class DbfFormIncoming implements EventSubscriberInterface
{

    /**
     * @var ClientVerificator
     */
    private ClientVerificator $clientVerificator;

    private $entityManager;

    private $userRepository;

    private $vehicleRepository;

    private $subjectRepository;

    private $commentRepository;

    private $cityRepository;

    private $civilityRepository;

    private $serviceRepository;

    private $dispatcher;

    private $recallPeriodRepository;

    public function __construct(
        ClientVerificator $clientVerificator,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        VehicleRepository $vehicleRepository,
        SubjectRepository $subjectRepository,
        CommentRepository $commentRepository,
        CityRepository $cityRepository,
        CivilityRepository $civilityRepository,
        ServiceRepository $serviceRepository,
        EventDispatcherInterface $eventDispatcher,
        RecallPeriodRepository $recallPeriodRepository
    ) {
        $this->clientVerificator      = $clientVerificator;
        $this->entityManager          = $entityManager;
        $this->userRepository         = $userRepository;
        $this->vehicleRepository      = $vehicleRepository;
        $this->subjectRepository      = $subjectRepository;
        $this->commentRepository      = $commentRepository;
        $this->cityRepository         = $cityRepository;
        $this->civilityRepository     = $civilityRepository;
        $this->serviceRepository      = $serviceRepository;
        $this->dispatcher             = $eventDispatcher;
        $this->recallPeriodRepository = $recallPeriodRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::DBF_FORM_SUBMIT => 'addCallFromDbfForm',
        ];
    }

    public function addCallFromDbfForm(GenericEvent $event)
    {
        $call = new Call();

        $call->setIsUrgent(0);
        $recallPeriod = $this->recallPeriodRepository->findOneByIdentifier('autourde');
        $call->setRecallPeriod($recallPeriod);

        $brand = $event->getSubject()->get('brand')->getData();


        // AUTEUR DU FORMULAIRE
        $author = $this->userRepository->findOneBy([
            'firstname' => 'INTERNET',
            'lastname' => 'DBF AUTOS'
        ]);
        if (!$author) {
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
            $this->entityManager->persist($author);
            $this->entityManager->flush();
        }
        $call->setAuthor($author);

        // DATE DE RAPPEL SOUHAITÉ
        $call->setRecallDate($event->getSubject()->get('callDate')->getData());

        // Heure de rappel souhaitée
        $hour = $event->getSubject()->get('callHour')->getData() < 10 ? '0' . $event->getSubject()->get('callHour')->getData() : $event->getSubject()->get('callHour')->getData();
        $minutes = $event->getSubject()->get('callMinutes')->getData() < 10 ? '0' . $event->getSubject()->get('callMinutes')->getData() : $event->getSubject()->get('callMinutes')->getData();
        $time = $hour . ':' . $minutes . ':00';
        $call->setRecallHour(new \DateTime('2000-01-01T' . $time));

        // CLIENT
        $clientName = $event->getSubject()->get('name')->getData();
        $clientPhoneNumber = $event->getSubject()->get('phone')->getData();
        $client = $this->clientVerificator->checkClient($clientName, $clientPhoneNumber);
        if ($client) {
            $call->setClient($client);
        } else {
            $client = new Client();
            $client->setName($clientName);
            $client->setPhone($clientPhoneNumber);
            $client->setCivility($event->getSubject()->get('civility')->getData());
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $call->setClient($client);
        }

        //VEHICULE
        $vehicle = $this->vehicleRepository->findOneByImmatriculation($event->getSubject()->get('immatriculation')->getData());
        if ($vehicle) {
            $call->setVehicle($vehicle);
        } else {
            $vehicle = new Vehicle();
            $vehicle->setClient($client);
            $vehicle->setImmatriculation($event->getSubject()->get('immatriculation')->getData());
            $vehicle->setHasCome(0);
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
            $call->setVehicle($vehicle);
        }
// SUBJECTS AND COMMENTS
        //SUBJECT
        $place = $event->getSubject()->get('place')->getData();
        $askFor = $event->getSubject()->get('askFor')->getData();
        $demand = 'Atelier';

        if (strstr($askFor, 'CARROSSERIE')) {
            $demand = 'Carrosserie';
        }
        $subject = $this->subjectRepository->findOneByName('Rendez-vous ' . $demand . ' ' . $place->getName());
        if (!$subject) {
            $subject = new Subject();
            $subject->setName('Rendez-vous ' . $demand . ' ' . $place->getName())
                ->setIsForAppWorkshop(1)
                ->setIsHidden(1)
                ->setCity($this->cityRepository->findOneByIdentifier('PHONECITY'));
            $this->entityManager->persist($subject);
            $this->entityManager->flush();
        }
        $call->setSubject($subject);

        //COMMENT
        $comment = $this->commentRepository->findOneByName('INTERNET');
        if (!$comment) {
            $comment = new Comment();
            $comment->setName('INTERNET')
                ->setIsHidden(1)
                ->setIdentifier('INTERNET');
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }
        $call->setComment($comment);

        //FREE COMMENT

        $textMessage = '<b>Rendez-vous souhaité à</b> : ' . $place->getTown()->getName() . ' - ' . $place->getName() . '<br>';
        $textMessage .= '<b>Commentaire laissé</b> : ' . $event->getSubject()->get('message')->getData();
        $call->setFreeComment($textMessage);

        //RECIPIENTS
        $callAskFor = $event->getSubject()->get('askFor')->getData();
        if ('CARROSSERIE - INTERNET' === $callAskFor) {
            // On cherche l'atelier carosserie de la concession pour la marque du client
            $workshop = $this->serviceRepository->getConcessionCarBodyWorkshops($place, ucfirst($brand));

            // S'il n'y en a pas
            if (!$workshop) {
                // On cherche l'atelier carosserie le plus proche de la concession choisie
                $workshop = $this->serviceRepository->getNearestCarBodyWorkshop($place);
            }
            // Si on a trouvé un atelier carosserie et qu'il correspond à la marque
            if ($workshop && ucfirst($brand) === $workshop->getBrand()) {
                $call->setService($workshop);
            } else { // Sinon on envoie à la cellule en changeant le message
                $message = $call->getFreeComment();
                $message = 'DEMANDE DE RDV CARROSSERIE MAIS AUCUN ATELIER N\'A ÉTÉ TROUVÉ POUR ' . $place->getName() . ' ET LA MARQUE ' . $brand . '<br>' . $message;
                $call->setFreeComment($message);
                $recipient = $this->userRepository->getRandomUser();
                $call->setRecipient($recipient);
            }

        } else {
            $recipient = $this->userRepository->getRandomUser();
            $call->setRecipient($recipient);
        }

        // PERSIST AND FLUSH
        $this->entityManager->persist($call);
        $this->entityManager->flush();

        // SEND EMAIL
        /*
        $event = new GenericEvent($call);
        $this->dispatcher->dispatch($event, Events::CALL_INCOMING);
        */
    }



}
