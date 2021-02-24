<?php

namespace App\Form;

use App\Entity\Concession;
use App\Entity\ConcessionHead;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConcessionHeadType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
            ->add('concession', EntityType::class, [
                'class'=> Concession::class,
                'choice_label'=>function ($concession) {
                    return $concession->getTown()->getName() . ' > ' . $concession->getName();
                },

            ])
        ;
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
            'data_class' => ConcessionHead::class,
        ]);
    }
}
