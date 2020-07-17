<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\ServiceHead;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceHeadType extends AbstractType
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
            ->add('service', EntityType::class, [
                'class'=> Service::class,
                'choice_label'=> function ($service) {
                    return $service->getConcessionAndCityFromService();
                }
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServiceHead::class,
            'allow_extra_fields' => true,
        ]);
    }
}
