<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\CityHead;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityHeadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class'=> User::class,
                'choice_label'=> function ($user) {
                    return $user->getFullName();
                },
            ])
            ->add('city', EntityType::class, [
                'class'=> City::class,
                'choice_label'=>'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CityHead::class,
        ]);
    }
}
