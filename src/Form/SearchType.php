<?php


namespace App\Form;

use App\Data\SearchData;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone', TextareaType::class, [
            'label' => false,
            'required' => false,
             'attr'=> ['placeholder'=>'numero de telephone']
        ])
        ->add('users', EntityType::class, [
            'class' => User::class,
            'required' => false,
            'choice_label'=>'lastname',
            'by_reference'=>false,
        ])

        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class'=> SearchData::class,
           'method'=> 'GET',
           'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
