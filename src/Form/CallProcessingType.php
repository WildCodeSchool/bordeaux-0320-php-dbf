<?php

namespace App\Form;

use App\Entity\Call;
use App\Entity\CallProcessing;
use App\Entity\ContactType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallProcessingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextType::class, [
                'attr' => [
                    'class' => 'active'
                ]
            ])
            ->add('contactType', EntityType::class, [
                'class'        => ContactType::class,
                'choice_label' => 'name',
            ])
            ->add('isAppointmentTaken', CheckboxType::class, [
                'label'    => 'Rendez vous pris ?',
                'required' => false,
                'mapped'   => false,
            ])
            ->add('referedCall', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CallProcessing::class,
        ]);
    }
}
