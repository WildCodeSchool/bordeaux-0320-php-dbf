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
                    'label' => ' ',
                    'choice_label' => function (Civility $civility) {
                        return $civility->getName();
                    },
                    'multiple' => false,
                    'required' => true,
                    'mapped' => false
                ])
                ->add('name', TextType::class, [
                    'label' => ' ',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => 'Votre nom',
                        'class' => 'upper'
                    ]
                ])
                ->add('phone', TelType::class, [
                    'label' => ' ',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => 'Téléphone',
                        'maxlength' => 10
                    ]
                ])
                ->add('immatriculation', TextType::class, [
                    'label' => ' ',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => 'Immatriculation'
                    ]
                ])
                ->add('callDate', DateType::class, [
                    'label' => 'Le ',
                    'widget' => 'single_text',
                    'data' => $this->getBaseDate(),
                    'mapped' => false,
                    'required' => true,
                    'attr' => [
                        'min' => date('Y-m-d')
                    ]
                ])
                ->add('callHour', ChoiceType::class, [
                    'label' => 'vers',
                    'required' => true,
                    'data' => $this->chooseHour(),
                    'mapped' => false,
                    'choices' => $this->makeHours()
                ])
                ->add('callMinutes', ChoiceType::class, [
                    'label' => ' ',
                    'required' => true,
                    'empty_data' => 0,
                    'mapped' => false,
                    'choices' => $this->makeMinutes()
                ])
                ->add('place', ChoiceType::class, [
                    'label' => 'Ville et concession souhaités',
                    'required' => true,
                    'mapped' => false,
                    'choices' => $this->getConcessions($options['brand'], $options['city'])
                ])
                ->add('message', TextareaType::class, [
                    'label' => 'Laisser un message complémentaire',
                    'required' => false,
                    'mapped' => false,
                    'attr' => [
                        'class' => "materialize-textarea",
                        'placeholder' => 'Laissez nous un message'
                    ]
                ])
                ->add('brand', HiddenType::class, [
                    'data' => $options['brand'],
                    'mapped' => false,
                    'required' => false,
                ])
                ->add('origin', HiddenType::class, [
                    'mapped' => false,
                    'required' => false,
                    'data' => $options['referer'] ?? $_SERVER['HTTP_REFERER'] ?? null
                ])
                ->add('reason', HiddenType::class, [
                    'mapped' => false,
                    'required' => false,
                    'data' => $options['reason']
                ])
        ;


    }

    private function chooseHour()
    {
        $hour = date('H');
        return $hour + 1;
    }

    private function getBaseDate()
    {
        $date = new \DateTime('now');
        $day = $date->format('N');
        if($day < 6) {
            return $date;
        }
        return $date->modify('next monday');
    }

    private function getDemands()
    {
        return [
            'Entretien' => 'ENTRETIEN - INTERNET',
            'Mécanique' => 'MECANIQUE - INTERNET',
            'Carrosserie' => 'CARROSSERIE - INTERNET',
        ];
    }

    private function getConcessions($brand, $city) {
        $concessions = $this->concessionRepository->findBy([
            'brand' => [$brand, 'Audi et Volkswagen'],
            'town'  => $city
        ]);

        $choices = [];
        foreach ($concessions as $concession) {
            $choices[ucfirst($brand) . ' DBF - ' . $city->getName() . ' ' . $concession->getName()] = $concession;
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
            'brand'    => null,
            'city'     => null,
            'reason'   => null,
            'referer'  => null,
            'csrf_protection' => false,
        ]);
    }
}
