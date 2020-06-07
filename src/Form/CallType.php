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
use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Service\DataMaker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CallType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('recallDate', HiddenType::class, ['attr' => ['id' => 'reCallDate']])
            ->add('freeComment', TextType::class, [
                'label' => 'commentaire éventuel'
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
                'by_reference' => false,
                ])
            ->add('vehicle', CollectionType::class, [
                'entry_type' => VehicleType::class,
                'entry_options' => ['label' => false],
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
            ->add('recallPeriod', EntityType::class, [
                'class'=> RecallPeriod::class,
                'choice_label' => 'name',
                'by_reference' => false,
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
