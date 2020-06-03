<?php

namespace App\Form;

use App\Entity\Call;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\RecallPeriod;
use App\Entity\Service;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('recallDate', null, [
                'date_format'=>'yyyy-MM-dd',
            ])
            ->add('freeComment', TextType::class, [
                'label' => 'commentaire éventuel'
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
                'by_reference' => false,
                ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'immatriculation',
                'by_reference' => false,
                'label' => 'immatriculation',
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'label' => 'Motif',
            ])
            ->add('comment', EntityType::class, [
                'class' => Comment::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'label' => 'Type'
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'label' => 'Service',
            ])
            ->add('recallPeriod', EntityType::class, [
                'class'=> RecallPeriod::class,
                'choice_label' => 'name',
                'by_reference' => false,
            ])
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'lastname',
                'by_reference' => false,
                'label' => 'Destinataire',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Call::class,
        ]);
    }
}
