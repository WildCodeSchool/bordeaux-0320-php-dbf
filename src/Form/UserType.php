<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        ->add('email', EmailType::class)
        ->add('password', PasswordType::class, [
            'label' => 'Mot de passe'
        ])
        ->add('firstname', TextType::class)
        ->add('lastname', TextType::class)
        ->add('phone', TextType::class, [
            'required' => false
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Collaborateur' => 'ROLE_COLLABORATOR',
                'Administrateur' => 'ROLE_ADMIN',
            ],
            'mapped'=>false
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
                $builder->
                add('concession', ChoiceType::class, [
                'choices' => $this->getConcessions($data->City),
                'mapped' => false
                ]);
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
