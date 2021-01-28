<?php


namespace App\Form;

use App\Data\SearchData;
use App\Entity\Call;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Concession;
use App\Entity\ContactType;
use App\Entity\Service;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Vehicle;
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


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone', TextType::class, [
            'label' => 'Téléphone',
            'required' => false])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'choice_label'=>'lastname',
                'by_reference'=>false,
                'label'=> 'Créateur'
            ])
            ->add('isUrgent', CheckboxType::class, [
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
            ->add('name', TextType::class, [
                'label' => 'Client',
                'required' => false,
            ])
            ->add('email', TextType::class, [
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
                    'Oui' => Vehicle::TRISWITCH_YES_VALUE,
                    'Non' => Vehicle::TRISWITCH_NO_VALUE,
                ],
                'required' => false,
                'label' => 'Déjà passé en atelier ?'
            ])
            ->add('town', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'by_reference' => false,
                'required' => false,
                'label' => 'Plaque'
            ])
            ->add('concession', EntityType::class, [
                'class' => Concession::class,
                'choice_label' => function ($concession) {
                    return $concession->getTown()->getName() . ' > ' . $concession->getName();
                },
                'by_reference' => false,
                'required' => false,
                'label' => 'Concession'
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => function ($service) {
                    return $service->getConcessionAndCityFromService();
                },
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
            ->add('commentTransfer', TextType::class, [
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
