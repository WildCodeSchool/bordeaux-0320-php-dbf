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

class DbfContactFormIncoming implements EventSubscriberInterface
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
            Events::DBF_CONTACT_FORM_SUBMIT => 'addCallFromContactForm',
        ];
    }

    public function addCallFromContactForm(GenericEvent $event)
    {
        $call = new Call();

        $call->setIsUrgent(0);

        $recallPeriod = $this->recallPeriodRepository->findOneByIdentifier('autourde');
        $call->setRecallPeriod($recallPeriod);


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
        $call->setRecallDate($event->getSubject()->get('date')->getData());

        // Heure de rappel souhaitée
        $time = RecallTimeMaker::makeContactTime($event);
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
        $vehicle = $this->vehicleRepository->findOneByImmatriculation($event->getSubject()->get('immat')->getData());
        if ($vehicle) {
            $call->setVehicle($vehicle);
        } else {
            $vehicle = VehicleMaker::make($client, $event->getSubject()->get('immat')->getData());

            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
            $call->setVehicle($vehicle);
        }

        // SUBJECTS AND COMMENTS
        //SUBJECT
        $subject = null;
        if (!$subject) {
            $name = 'Formulaire de contact site DBF';
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
        $textMessage = '<b>Commentaire laissé</b> : ' . $event->getSubject()->get('message')->getData();
        $call->setFreeComment($textMessage);

        //RECIPIENTS
        $service = $event->getSubject()->get('service')->getData();
        $call->setService($service);

        //ORIGIN
        $call->setOrigin($event->getSubject()->get('origin')->getData());

        // PERSIST AND FLUSH
        $this->entityManager->persist($call);
        $this->entityManager->flush();

        // SEND EMAIL

        $event = new GenericEvent($call);
        $this->dispatcher->dispatch($event, Events::CALL_INCOMING);

    }
}
