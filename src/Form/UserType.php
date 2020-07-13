<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Concession;
use App\Entity\Right;
use App\Entity\Service;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
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
        ->add('email')
        ->add('password', PasswordType::class, [
            'label' => 'Mot de passe'
        ])
        ->add('firstname', TextType::class)
        ->add('lastname', TextType::class)
        ->add('phone', TextType::class, [
            'required' => false
        ])
        ->add('roles', CollectionType::class, [
            'entry_type' => ChoiceType::class,
            'entry_options' => [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Collaborateur' => 'ROLE_COLLABORATOR',
                ],
            ],
        ])
        ->add('city', ChoiceType::class, [
            'choices' => $this->getAllCities(),
            'mapped' => false,
        ])
        ->add('service', EntityType::class, [
            'class' => Service::class,
            'choice_label' => 'name',
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
            'mapped' => false
            ]);
        }
    }



            /**
            ->add('city', EntityType::class, [
                'class'=>City::class,
                'choice_label' => 'name',
                'placeholder'=> 'sélectionner la plaque',
                'mapped'=> false,
                'required'=>false
            ]);

        $builder->get('city')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addConcessionField($form->getParent(), $form->getData());
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();

                $service = $data->getService();
                $form = $event->getForm();
                if ($service) {
                    $concession = $service->getConcession();
                    $city = $concession->getTown();
                    $this->addConcessionField($form, $city);
                    $this->addServiceField($form, $concession);
                    $form->get('city')->setData($city);
                    $form->get('concession')->setData($concession);
                } else {
                    $this->addConcessionField($form, null);
                    $this->addServiceField($form, null);
                }
            }
        );
    }
**/
    /*
     * @param FormInterface $form
     * @param City $city
     */
/**
    private function addConcessionField(FormInterface $form, ?City $city)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'concession',
            EntityType::class,
            null,
            [
                'class'=> Concession::class,
                'choice_label'=>'name',
                'placeholder'=>$city ? 'sélectionner une concession': 'sélectionner d\'abord une plaque',
                'mapped'=> false,
                'required'=> false,
                'auto_initialize'=> false,
                'choices'=> $city ? $city->getConcessions(): [],
            ]
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addServiceField($form->getParent(), $form->getData());
            }
        );
        $form->add($builder->getForm());
    }

    private function addServiceField(FormInterface $form, ?Concession $concession)
    {
        $form->add('service', EntityType::class, [
            'class'=> Service::class,
            'placeholder'=> $concession ?'sélectionner un service': 'sélectionner d\'abord votre concession',
            'choices'=> $concession ? $concession->getServices() : [],
            'choice_label'=>'name'
        ]);
    }
 **/


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true,
        ]);
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
}
