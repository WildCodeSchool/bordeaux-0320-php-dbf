<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', EntityType::class, [
                'class'         => Civility::class,
                'label'=>'Civilité',
                'choice_label'  => function (Civility $civility) {
                    return $civility->getName();
                },
                'multiple'      => false,
            ])
            ->add('name', TextType::class, [
                'label'=>'Nom ou Raison sociale ',
            ])
            ->add('phone', TextType::class, [
                'label'=>'Téléphone',
            ])
            ->add('phone2', TextType::class, [
                'label'=>'Téléphone 2',
                'required'   => false,
            ])
            ->add('email', EmailType::class, [
                'label'=>'Email',
                'required'   => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
