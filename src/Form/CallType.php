<?php

namespace App\Form;

use App\Entity\Call;
use App\Entity\Comment;
use App\Entity\RecallPeriod;
use App\Entity\Service;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CallType extends AbstractType
{

    private $cityRepository;
    private $concessionRepository;
    private $serviceRepository;
    private $userRepository;
    private $security;

    public function __construct(
        CityRepository $cityRepository,
        ConcessionRepository $concessionRepository,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository,
        Security $security
    ) {
        $this->cityRepository       = $cityRepository;
        $this->concessionRepository = $concessionRepository;
        $this->serviceRepository    = $serviceRepository;
        $this->userRepository       = $userRepository;
        $this->security             = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $post = file_get_contents('php://input');
        if ($post) {
            $data = json_decode($post);
        }
        $user = $this->security->getUser();
        $builder
            ->add('freeComment', TextType::class, [
                'label' => 'Commentaire éventuel',
                'required'   => false,
            ])
            ->add('city', ChoiceType::class, [
                'choices' => $this->getAllCities(),
                'mapped'  => false,
                'data' => $user->getService()->getConcession()->getTown()->getId(),
            ])
            ->add('concession', ChoiceType::class, [
                'choices' => $this->getConcessions($user->getService()->getConcession()->getTown()->getId()),
                'mapped' => false,
                'data' => $user->getService()->getConcession()->getId(),
            ])
            ->add('service_choice', ChoiceType::class, [
                'choices' => $this->getServices($user->getService()->getConcession()->getId()),
                'mapped'  => false
            ])

            ->add('service', EntityType::class, [
                'class'         => Service::class,
                'choice_label' => 'name'
            ])
            ->add('recipient', EntityType::class, [
                'class'   => User::class,
                'choice_label' => 'lastname'
            ]);

        if (isset($data->City)) {
            $city = $this->cityRepository->findOneById($data->City);
            if (!$city->isPhoneCity()) {
                $builder->
                add('concession', ChoiceType::class, [
                    'choices' => $this->getConcessions($data->City),
                    'mapped' => false
                ]);
            }
        }
        if (isset($data->Concession)) {
            $builder->
            add('service_choice', ChoiceType::class, [
                'choices' => $this->getServices($data->Concession),
                'mapped'  => false
            ]);
        }
        if (isset($data->Service)) {
            $builder->add('recipient_choice', ChoiceType::class, [
                'choices' => $this->getRecipients($data->Service),
                'mapped'  => false
            ]);
        }
        $builder->add('subject', EntityType::class, [
            'class' => Subject::class,
            'choice_label' => 'name',
            'by_reference' => false,
            'label' => 'Motif',
        ])
            ->add('comment', EntityType::class, [
                'class' => Comment::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'label' => 'Type'
            ])
            ->add('recallPeriod', EntityType::class, [
                'class'=> RecallPeriod::class,
                'choice_label' => 'name',
                'by_reference' => false,
            ])
            ->add('client_id', hiddenType::class, [
                'mapped'  => false
            ])
            ->add('client', ClientType::class)
            ->add('vehicle', VehicleTypeForCallType::class)
            ->add('vehicle_id', hiddenType::class, [
                'mapped'  => false
            ])

            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'label' => 'Motif',
            ])
            ->add('comment', EntityType::class, [
                'class' => Comment::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'label' => 'Type'
            ])
            ->add('recallDate', DateType::class, [
                'label'=>'date de rappel',
                'widget' => 'single_text',
                'data' => new DateTime(),
            ])
            ->add('recallHour', TimeType::class, [
                'widget'=>'choice',
                'label'=>'heure de rappel',
                'hours'=>[8,9,10,11,12,13,14,15,16,17,18,19,20],
                'minutes'=>[0,15,30,45],
                'data'=> new DateTime('Europe/Paris')
            ])
            ->add('recallPeriod', EntityType::class, [
                'class'=> RecallPeriod::class,
                'choice_label' => 'name',
                'by_reference' => false,
            ])
        ;
        /**
        $builder->get('client')->addEventListener(
        For
        )
         * **/
    }

    public function getAllCities()
    {
        $cities = $this->cityRepository->findAll();
        $choices = [];
        $choices['Choisir une plaque'] = '';
        foreach ($cities as $city) {
            $choices[$city->getName()] = $city->getId();
        }
        return $choices;
    }

    public function getConcessions($cityId = null)
    {
        if (!$cityId) {
            $concessions = $this->concessionRepository->findAll();
        } else {
            $concessions = $this->concessionRepository->findBy(['town' => $cityId]);
        }
        $choices = [];
        $choices['Choisir une concession'] = '';
        foreach ($concessions as $concession) {
            $choices[$concession->getName()] = $concession->getId();
        }
        return $choices;
    }


    public function getServices($concessionId = null)
    {
        if (is_null($concessionId)) {
            $services = $this->serviceRepository->findAll();
        } else {
            $services = $this->serviceRepository->findBy(['concession' => $concessionId]);
        }
        $choices = [];
        $choices['Choisir un service'] = '';
        foreach ($services as $service) {
            $choices[$service->getName()] = $service->getId();
        }

        return $choices;
    }



    public function getRecipients($serviceId = null)
    {
        if (is_null($serviceId)) {
            $recipients = $this->userRepository->findOperationnalUsers();
        } else {
            $recipients = $this->userRepository->findOperationnalUsersInService($serviceId);
        }
        $choices = [];
        $choices['Choisir un destinataire'] = '';
        $choices['Tous les collaborateurs'] = 'service-' . $serviceId;
        foreach ($recipients as $user) {
            $choices[$user->getFirstname() . ' ' . $user->getLastname()] = $user->getId();
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Call::class,
            'allow_extra_fields' => true,
        ]);
    }
}
