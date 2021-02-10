<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Concession;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConcessionType extends AbstractType
{

    /**
     * @var ServiceRepository
     */
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de concession'

            ])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('postcode', TextType::class, ['label' => 'Code Postal'])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('brand', ChoiceType::class, [
                'label' => 'Marque',
                'choices' => [
                    'Audi' => 'Audi',
                    'Volkswagen' => 'Volkswagen',
                    'Audi et Volkswagen' => 'Audi et Volkswagen'
                ]
            ])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('town', EntityType::class, [
                'class'=> City::class,
                'choice_label'=> 'name',
                'label'=> 'Nom de la plaque'
            ])
            ->add('nearestCarBodyWorkshop', ChoiceType::class, [
                'required' => false,
                'choices' => $this->getCarBodyWorkshops(),
                'label' => 'Choisir l\'atelier carosserie le plus proche si cette concession n\'en a pas (optionnel)'
            ])
        ;
    }

    public function getCarBodyWorkshops()
    {
        $workshops = $this->serviceRepository->getCarBodyWorkshops();
        $choices = [];
        foreach ($workshops as $workshop)
        {
            $choices[$workshop->getConcession()->getTown()->getName() . ' - ' . $workshop->getConcession()->getName() . ' - Service ' . $workshop->getName()] = $workshop;
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Concession::class,
        ]);
    }
}
