<?php


namespace App\Form;

use App\Data\SearchData;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Concession;
use App\Entity\ContactType;
use App\Entity\Service;
use App\Entity\Subject;
use App\Entity\User;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    const TRISWITCH_YES_VALUE = 1;
    const TRISWITCH_NO_VALUE = 2;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone', TextType::class, [
            'label' => 'Téléphone',
            'required' => false])
            ->add('authors', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'choice_label'=>'lastname',
                'by_reference'=>false,
                'label'=> 'Créateur'
            ])
            ->add('urgent', CheckboxType::class, [
                'label'=>'Urgent',
                'required'=> false,
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'required' => false,
                'label' => 'Motif',
            ])
            ->add('comment', EntityType::class, [
                'class' => Comment::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'required' => false,
                'label' => 'Type'
            ])
            ->add('clientName', TextType::class, [
                'label' => 'Client',
                'required' => false,
            ])
            ->add('clientEmail', TextType::class, [
                'label'=> 'Email',
                'required'=> false,
            ])
            ->add('immatriculation', TextType::class, [
                'label'=> 'Immatriculation',
                'required'=> false
            ])
            ->add('chassis', TextType::class, [
                'label'=>'Chassis',
                'required'=> false,
            ])
            ->add('hasCome', ChoiceType::class, [
                'choices'  => [
                    '' => null,
                    'Oui' => self::TRISWITCH_YES_VALUE,
                    'Non' => self::TRISWITCH_NO_VALUE,
                ],
                'required' => false,
                'label' => 'Déjà passé en atelier ?'
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'required' => false,
                'label' => 'Plaque'
            ])
            ->add('concession', EntityType::class, [
                'class' => Concession::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'required' => false,
                'label' => 'Concession'
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'required' => false,
                'label' => 'Service'
            ])
            ->add('isAppointmentTaken', ChoiceType::class, [
                'required'=>false,
                'choices'  => [
                    ''=>null,
                    'Oui' => true,
                    'Non' => false,
                ], 'label' => 'RDV'
            ])
            ->add('freeComment', TextType::class, [
                'label'=> 'Commentaire éventuel',
                'required'=>false
            ])
            ->add('contactType', EntityType::class, [
                'class'=> ContactType::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'label' => 'contact',
                'required'=> false
            ])
            ->add('commentTransfert', TextType::class, [
                'label' => 'Commentaire transfert',
                'required' => false
            ])
            ->add('dateFrom', DateType::class, [
                'required'=>false,
                'label'=>'Date du',
                'widget' => 'single_text',
            ])
            ->add('dateTo', DateType::class, [
                'required'=>false,
                'label'=>'Au',
                'widget' => 'single_text',
                'data' => new DateTime(),
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> SearchData::class,
            'method'=> 'POST',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
