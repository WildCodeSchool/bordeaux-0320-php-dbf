<?php

namespace App\Form\LandingForm;

use App\Entity\Call;
use App\Entity\Civility;
use App\Entity\Concession;
use App\Entity\DbfContact;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use \DateTime;

class DbfContactType extends AbstractType
{

    private ConcessionRepository $concessionRepository;

    private ServiceRepository $serviceRepository;

    public function __construct(ConcessionRepository $concessionRepository, ServiceRepository $serviceRepository)
    {
        $this->concessionRepository = $concessionRepository;
        $this->serviceRepository    = $serviceRepository;
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
                ->add('immat', TextType::class, [
                    'label' => ' ',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'placeholder' => 'Immatriculation'
                    ]
                ])
                ->add('date', DateType::class, [
                    'label' => 'Le ',
                    'widget' => 'single_text',
                    'data' => $this->getBaseDate(),
                    'mapped' => false,
                    'required' => true,
                    'attr' => [
                        'min' => date('Y-m-d')
                    ]
                ])
                ->add('hour', ChoiceType::class, [
                    'label' => 'vers',
                    'required' => true,
                    'data' => $this->chooseHour(),
                    'mapped' => false,
                    'choices' => $this->makeHours()
                ])
                ->add('minute', ChoiceType::class, [
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
                    'choices' => $this->getConcessions()
                ])

                ->add('origin', HiddenType::class, [
                    'mapped' => false,
                    'required' => false,
                    'data' => $options['referer'] ?? null
                ])
        ;

        $builder->get('place')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                $field = $event->getForm();
                $form = $field->getParent();
                $services = $this->getServices($field->getData());
                $form
                    ->add('service', ChoiceType::class, [
                    'choices' => $services,
                    'mapped' => true,
                    'required' => true
                    ])
                    ->add('message', TextareaType::class, [
                        'label' => 'Laisser un message complémentaire',
                        'required' => false,
                        'mapped' => false,
                        'attr' => [
                            'class' => "materialize-textarea",
                            'placeholder' => 'Votre message'
                        ]
                    ])
                ;
            }
        );
    }

    private function getServices(Concession $concession)
    {
        $choices = [];
        $services = $this->serviceRepository->getServicesToContact($concession);

        foreach ($services as $service) {
            $service = $this->serviceRepository->findOneById($service['id']);
            $choices[$service->getName()] = $service;
        }
        return $choices;
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

    private function getConcessions() {
        $concessions = $this->concessionRepository->findAllConcessions();
        $choices = [];
        $choices['Choisir une concession'] = null;
        foreach ($concessions as $concession) {
            $label = 'DBF ' . $concession[0]->getBrand() . ' - ' . $concession[0]->getTown()->getName() . ' - ' . $concession[0]->getName();
            $choices[$label] = $concession[0];
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
            'data_class' => DbfContact::class,
            'referer'  => null,
            'concession' => null,
            'allow_extra_fields' => true,
            'csrf_protection' => false,
        ]);
    }
}
