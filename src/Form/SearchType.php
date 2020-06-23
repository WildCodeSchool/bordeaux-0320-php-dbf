<?php


namespace App\Form;

use App\Data\SearchData;
use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            'attr'=> ['placeholder'=>'numéro de telephone']
            ])
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
