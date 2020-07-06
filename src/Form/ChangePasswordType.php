<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Ancien mot de passe',

                'mapped' => false

            ])
            ->add('plainPassword', RepeatedType::class, [
                'first_options'  => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmation du nouveau mot de passe'],

                'type' => PasswordType::class,

                'invalid_message' => 'Les deux mots de passe doivent être identiques',

                'options' => [

                    'attr' => [

                        'class' => 'password-field'

                    ]

                ],

                'required' => true,

                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choisissez un nouveau mot de passe !'
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Veuillez mettre un mot de passe d\'au moins 6 caractrères'
                    ])
                ]

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
