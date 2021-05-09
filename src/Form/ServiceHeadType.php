<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\ServiceHead;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ServiceRepository;

class ServiceHeadType extends AbstractType
{
    private $userRepository;
    private $serviceRepository;

    public function __construct(UserRepository $userRepository, ServiceRepository $serviceRepository)
    {
        $this->userRepository    = $userRepository;
        $this->serviceRepository = $serviceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'user',
                'choice_label' => function($user) {
                    return strtoupper($user->getLastname()) . ' ' . $user->getFirstname();
                },
                'query_builder' => function(UserRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ])
            ->add('service', ChoiceType::class, [
                'choices' => $this->servicesChoices()
                ]);
    }

    private function servicesChoices()
    {
        $services = $this->serviceRepository->findAllOrderByCityAndConcession();
        $choices = [];
        foreach ($services as $service) {
            $name = $service->getConcession()->getTown()->getName() . ' > ' . $service->getConcession()->getName() . ' > ' . $service->getName();
            $choices[$name] = $service;
        }
        return $choices;
    }

    private function getUsers() {
        $users = $this->userRepository->findBy([], ['lastname' => 'ASC']);
        $choices = [];
        foreach ($users as $user) {
            $choices[strtoupper($user->getLastname()) . ' ' . $user->getFirstname()] = $user;
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServiceHead::class,
            'allow_extra_fields' => true,
        ]);
    }
}
