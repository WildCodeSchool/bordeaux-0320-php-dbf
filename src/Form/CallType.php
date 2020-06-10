<?php

namespace App\Form;

use App\Entity\Call;
use App\Entity\Comment;
use App\Entity\RecallPeriod;
use App\Entity\Subject;
use App\Service\DataMaker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CallType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('freeComment', TextType::class, [
                'label' => 'commentaire éventuel',
                'required'   => false,
            ])
            ->add('client', ClientType::class)
            ->add('vehicle', VehicleTypeForCallType::class)
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
            ->add('recallDate', DateType::class, ['label'=>'date de rappel'])
            ->add('recallHour', TimeType::class, ['label'=>'heure de rappel'])
            ->add('recallPeriod', EntityType::class, [
                'class'=> RecallPeriod::class,
                'choice_label' => 'name',
                'by_reference' => false,
            ])
            ->add('createdAt', HiddenType::class)
        ;
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Call::class,
        ]);
    }
}
