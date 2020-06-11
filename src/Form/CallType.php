<?php

namespace App\Form;

use App\Entity\Call;
use App\Entity\Comment;
use App\Entity\RecallPeriod;
use App\Entity\Subject;
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

class CallType extends AbstractType
{

    private $cityRepository;
    private $concessionRepository;
    private $serviceRepository;
    private $userRepository;

    public function __construct(
        CityRepository $cityRepository,
        ConcessionRepository $concessionRepository,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository
    ) {
        $this->cityRepository       = $cityRepository;
        $this->concessionRepository = $concessionRepository;
        $this->serviceRepository    = $serviceRepository;
        $this->userRepository       = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $post = file_get_contents('php://input');
        if ($post) {
            $data = json_decode($post);
        }

        $builder
            ->add('freeComment', TextType::class, [
                'label' => 'Commentaire éventuel',
                'required'   => false,
            ])
            ->add('city', ChoiceType::class, [
                'choices' => $this->getAllCities(),
                'mapped'  => false
            ]);
        if (isset($data->City)) {
            $builder->
            add('concession', ChoiceType::class, [
                'choices' => $this->getConcessions($data->City),
                'mapped'  => false
            ]);
        }
        if (isset($data->Concession)) {
            $builder->
            add('service', ChoiceType::class, [
                'choices' => $this->getServices()
            ]);
        }
        if (isset($data->Service)) {
            $builder->add('recipient', ChoiceType::class, [
                'choices' => $this->getRecipients()
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

        ->add('client', ClientType::class)
        ->add('vehicle', VehicleTypeForCallType::class)
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
            'widget'=>'single_text',
            'label'=>'heure de rappel',
            'data' => new DateTime(),
            'attr' => ['min' => "8:00", 'max'=>"20:00"],
        ])
        ->add('recallPeriod', EntityType::class, [
            'class'=> RecallPeriod::class,
            'choice_label' => 'name',
            'by_reference' => false,
        ])
        ->add('createdAt', HiddenType::class)
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
        if (!$concessionId) {
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
        if (!$serviceId) {
            $recipients = $this->userRepository->findAll();
        } else {
            $recipients = $this->userRepository->findBy(['service' => $serviceId]);
        }
        $choices = [];
        $choices['Choisir un destinataire'] = '';
        foreach ($recipients as $user) {
            $choices[$user->getFirstname() . ' ' . $user->getLastname()] = $user->getId();
        }
        return $choices;
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Call::class,
        ]);
    }
}
