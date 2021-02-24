<?php

namespace App\Form\LandingForm;

use App\Entity\Call;
use App\Entity\Civility;
use App\Repository\ConcessionRepository;
use App\Service\Landing\OriginChecker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use \DateTime;

class DbfBrandType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand', ChoiceType::class, [
                'required' => false,
                'mapped' => false,
                'choices' => [
                    'choisir la marque de votre véhicule' => '',
                    'Audi' => '/dbf/form/audi/' . $options['referer'],
                    'Volkswagen' => '/dbf/form/volkswagen/' . $options['referer'],
                ],
                'attr' => [
                    'onChange' => 'document.location.href=this.value;'
                ]
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'referer' => null,
            'csrf_protection' => false,
        ]);
    }
}
