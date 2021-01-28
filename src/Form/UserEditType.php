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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
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
        $builder
            ->add('email', EmailType::class)
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
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => function ($service) {
                    return $service->getConcessionAndCityFromService();
                }

            ])
            ->add('canBeRecipient');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true,
        ]);
    }
}
