<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [

                'mapped' => false

            ])
            ->add('plainPassword', RepeatedType::class, [

                'type' => PasswordType::class,

                'invalid_message' => 'Les deux mots de passe doivent être identiques',

                'options' => array(

                    'attr' => array(

                        'class' => 'password-field'

                    )

                ),

                'required' => true,

                'mapped' => false,

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
