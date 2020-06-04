<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', EntityType::class, [
                'class'         => Civility::class,
                'choice_label'  => function (Civility $civility) {
                    return $civility->getName();
                },
                'multiple'      => false,
            ])
            ->add('name')
            ->add('phone')
            ->add('phone2')
            ->add('email')
            ->add('postcode');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
