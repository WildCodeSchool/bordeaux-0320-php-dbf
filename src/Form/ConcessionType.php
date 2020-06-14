<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Concession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConcessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de concession'])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('postcode', TextType::class, ['label' => 'Code Postal'])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('brand', TextType::class, ['label' => 'Marque'])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('town', EntityType::class, [
                'class'=> City::class,
                'choice_label'=> 'name',
                'label'=> 'Nom de la plaque'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Concession::class,
        ]);
    }
}
