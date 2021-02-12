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
use App\Service\Landing\FormTreatment\Tools\ClientMaker;
use App\Service\Landing\FormTreatment\Tools\CommentMaker;
use App\Service\Landing\FormTreatment\Tools\RecallTimeMaker;
use App\Service\Landing\FormTreatment\Tools\SubjectMaker;
use App\Service\Landing\FormTreatment\Tools\VehicleMaker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use App\Service\Landing\FormTreatment\Tools\InternetUserCreator;

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

    private $userCreator;

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
        RecallPeriodRepository $recallPeriodRepository,
        InternetUserCreator $userCreator
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
        $this->userCreator            = $userCreator;
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
            $author = $this->userCreator->create();
            $this->entityManager->persist($author);
            $this->entityManager->flush();
        }
        $call->setAuthor($author);

        // DATE DE RAPPEL SOUHAITÉ
        $call->setRecallDate($event->getSubject()->get('callDate')->getData());

        // Heure de rappel souhaitée
        $time = RecallTimeMaker::makeTime($event);
        $call->setRecallHour(new \DateTime('2000-01-01T' . $time));

        // CLIENT
        $clientName        = $event->getSubject()->get('name')->getData();
        $clientPhoneNumber = $event->getSubject()->get('phone')->getData();
        $client            = $this->clientVerificator->checkClient($clientName, $clientPhoneNumber);
        if ($client) {
            $call->setClient($client);
        } else {
            $client = ClientMaker::make($clientName, $clientPhoneNumber, $event->getSubject()->get('civility')->getData());

            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $call->setClient($client);
        }

        //VEHICULE
        $vehicle = $this->vehicleRepository->findOneByImmatriculation($event->getSubject()->get('immatriculation')->getData());
        if ($vehicle) {
            $call->setVehicle($vehicle);
        } else {
            $vehicle = VehicleMaker::make($client, $event->getSubject()->get('immatriculation')->getData());

            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
            $call->setVehicle($vehicle);
        }
// SUBJECTS AND COMMENTS
        //SUBJECT
        $place  = $event->getSubject()->get('place')->getData();
        $askFor = $event->getSubject()->get('askFor')->getData();
        $demand = 'Atelier';

        if (strstr($askFor, 'CARROSSERIE')) {
            $demand = 'Carrosserie';
        }
        $subject = $this->subjectRepository->findOneByName('Rendez-vous ' . $demand . ' ' . $place->getName());
        if (!$subject) {
            $name = 'Rendez-vous ' . $demand . ' ' . $place->getName();
            $subject = SubjectMaker::make($name, 1, 1, $this->cityRepository->findOneByIdentifier('PHONECITY'));

            $this->entityManager->persist($subject);
            $this->entityManager->flush();
        }
        $call->setSubject($subject);

        //COMMENT
        $comment = $this->commentRepository->findOneByName('INTERNET');
        if (!$comment) {
            $comment = CommentMaker::make('INTERNET', 1, 'INTERNET');

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

        $recipient = $this->userRepository->getRandomUser();
        $call->setRecipient($recipient);

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
                $call->setRecipient(null);
            }
            // Sinon on envoie à la cellule en changeant le message
            else {
                $message = $call->getFreeComment();
                $message = 'DEMANDE DE RDV CARROSSERIE MAIS AUCUN ATELIER N\'A ÉTÉ TROUVÉ POUR ' . $place->getName() . ' ET LA MARQUE ' . $brand . '<br>' . $message;
                $call->setFreeComment($message);
                $recipient = $this->userRepository->getRandomUser();
                $call->setRecipient($recipient);
            }
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
