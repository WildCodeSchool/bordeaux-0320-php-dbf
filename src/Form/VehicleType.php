<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation'
            ])
            ->add('chassis')
            ->add('hasCome', HiddenType::class, [
                'attr' => ['id' => 'hasCome']
            ])
            ->add('client', EntityType::class, [
                'class'=> Client::class,
                'choice_label' => 'name',
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
