<?php

namespace App\Form;

use App\Entity\Concession;
use App\Entity\ConcessionHead;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConcessionHeadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class'=> User::class,
                'choice_label'=> function ($user) {
                    return $user->getFullName();
                },
            ])
            ->add('concession', EntityType::class, [
                'class'=> Concession::class,
                'choice_label'=>function ($concession) {
                    return $concession->getTown()->getName() . ' > ' . $concession->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConcessionHead::class,
        ]);
    }
}
