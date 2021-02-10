<?php

namespace App\Form;

use App\Entity\Concession;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de service',
        ])
            ->add('brand', ChoiceType::class, [
                'choices' => [
                    'Audi' => 'Audi',
                    'Volkswagen' => 'Volkswagen'
                ],
                'required' => false,
                'label' => 'marque'
            ])
            ->add('concession', EntityType::class, [
               'class'=> Concession::class,
                'choice_label'=> 'name'
            ])
            ->add('isCarBodyWorkshop', CheckboxType::class, [
                'label' => 'Service carosserie',
                'required' => false
            ])
            ->add('isDirection', CheckboxType::class, [
                'label' => 'Service direction',
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
