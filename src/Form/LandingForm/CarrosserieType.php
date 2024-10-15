<?php

namespace App\Form\LandingForm;

use App\Entity\Call;
use App\Entity\Civility;
use App\Entity\Concession;
use App\Repository\ConcessionRepository;
use App\Service\Landing\OriginChecker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use \DateTime;

class CarrosserieType extends AbstractType
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
                        'placeholder' => 'Votre nom *',
                        'class' => 'upper required'
                    ]
                ])
                ->add('phone', TelType::class, [
                    'label' => ' ',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => 'Téléphone *',
                        'maxlength' => 10,
                        'class' => 'required'
                    ]
                ])
                ->add('email', EmailType::class, [
                'label' => ' ',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Votre email *',
                    'class' => 'required'
                ]
            ])
                ->add('immatriculation', TextType::class, [
                    'label' => ' ',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => 'Immatriculation *',
                        'class' => 'required'
                    ]
                ])

                ->add('place', ChoiceType::class, [
                    'label' => 'Ville et concession souhaités',
                    'required' => true,
                    'mapped' => false,
                    'choices' => $this->getConcessions($options['data']['city'])
                ])

                ->add('askFor', ChoiceType::class, [
                    'label' => 'Type d\'intervention',
                    'required' => true,
                    'mapped' => false,
                    'choices' => $this->getDemands()
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
                ->add('message', TextareaType::class, [
                    'label' => 'Laisser un message complémentaire',
                    'required' => false,
                    'mapped' => false,
                    'attr' => [
                        'class' => "materialize-textarea",
                        'placeholder' => 'Veuillez préciser tous les détails importants concernant votre demande',
                        'maxlength' => 250
                    ]
                ])

                ->add('origin', HiddenType::class, [
                    'mapped' => false,
                    'required' => false,
                    'data' => $options['referer'] ?? null
                ])
        ;


    }


    private function getDemands()
    {
        return [
            'Réparation' => 'REPARATION - INTERNET',
            'Débosselage' => 'DEBOSSELAGE - INTERNET',
            'Peinture' => 'PEINTURE - INTERNET',
            'Pneumatiques' => 'PNEUMATIQUES - INTERNET',
            'Pare-brise' => 'PARE-BRISE - INTERNET',
            'Climatisation' => 'CLIMATISATION - INTERNET',
            'Géométrie' => 'GEOMETRIE - INTERNET',
            'Autres' => 'AUTRES - INTERNET',
        ];
    }

    private function getConcessions($cityName) {
        $concessions = $this->concessionRepository->findCarosserieConcessions($cityName);

        $choices = [];
        /** @var Concession $concession */
        foreach ($concessions as $concession) {

            $choices[$concession->getTown()->getName() . ' - ' . $concession->getName()] = $concession;
        }
        return $choices;
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
    private function makeHours() {
        $choices = [];
        for ($i = 8; $i <= 17; $i++) {
            $choices[$i . 'h'] = $i;
        }
        return $choices;
    }

    private function chooseHour()
    {
        $hour = date('H');
        return $hour + 1;
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
            'data_class' => null,
            'city'    => null,
            'referer'  => null,
            'csrf_protection' => false,

        ]);
    }
}
