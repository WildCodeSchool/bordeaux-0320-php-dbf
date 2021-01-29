<?php

namespace App\Form\LandingForm;

use App\Entity\Call;
use App\Entity\Civility;
use App\Repository\ConcessionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use \DateTime;

class LandingType extends AbstractType
{

    private $concessionRepository;

    public function __construct(ConcessionRepository $concessionRepository)
    {
        $this->concessionRepository = $concessionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', EntityType::class, [
                'class' => Civility::class,
                'label'=>' ',
                'choice_label' => function (Civility $civility) {
                    return $civility->getName();
                },
                'multiple' => false,
                'required' => true,
                'mapped'  => false
            ])
            ->add('name', TextType::class, [
                'label' => 'Votre nom',
                'required' => true,
                'mapped'  => false
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre téléphone',
                'required' => true,
                'mapped'  => false,
                'attr' => [
                    'placeholder' => '0000000000',
                    'maxlength' => 10
                ]
            ])
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation du véhicule',
                'required' => true,
                'mapped'  => false,
                'attr' => [
                    'placeholder' => 'AA-555-BB'
                ]
            ])
            ->add('callDate', DateType::class, [
                'label' => 'Souhaite être rappelé le',
                'widget' => 'single_text',
                'data' => new DateTime(),
                'mapped'  => false,
                'required' => true
            ])
            ->add('callHour', ChoiceType::class, [
                'label' => 'vers',
                'required' => true,
                'mapped'  => false,
                'choices' => $this->makeHours()
            ])
            ->add('callMinutes', ChoiceType::class, [
                'label' => ' ',
                'required' => true,
                'mapped'  => false,
                'choices' => $this->makeMinutes()
            ])
            ->add('place', ChoiceType::class, [
                'label' => 'Lieu souhaité',
                'required' => true,
                'mapped'  => false,
                'choices' => $this->getConcessions(' Audi')
            ])
            ->add('askFor', ChoiceType::class, [
                'label' => 'motif',
                'required' => true,
                'mapped'  => false,
                'choices' => $this->getDemands()
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Laisser un message complémentaire',
                'required' => false,
                'mapped'  => false,
                'attr' => [
                    'class' => "materialize-textarea"
                ]
            ])
        ;
    }

    private function getDemands()
    {
        return [
            'Entretien' => 'entretien',
            'Mécanique' => 'mecanique',
            'Carosserie' => 'carosserie',
        ];
    }

    private function getConcessions($brand) {

        $concessions = $this->concessionRepository->findByBrand($brand);
        $choices = [];
        foreach ($concessions as $concession) {
            $choices[$concession['name'] . ' - ' . $concession[0]->getName()] = $concession[0];
        }
        return $choices;
    }

    private function makeHours() {
        $choices = [];
        for ($i = 8; $i <= 17; $i++) {
            $choices[$i . 'h'] = $i;
        }
        return $choices;
    }

    private function makeMinutes() {
        return [
            '00' => 0,
            '15' => 15,
            '30' => 30,
            '45' => 45
        ];
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Call::class,
        ]);
    }
}
