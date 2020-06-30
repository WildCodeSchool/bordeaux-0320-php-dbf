<?php

namespace App\Form;

use App\Entity\Call;
use App\Entity\CallProcessing;
use App\Entity\ContactType;
use App\Repository\ContactTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallProcessingType extends AbstractType
{
    private $contactTypeRepository;

    public function __construct(ContactTypeRepository $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextType::class, [
                'attr' => [
                    'class' => 'active'
                ]
            ])
            ->add('contactType', ChoiceType::class, [
                'choices' => $this->getContactTypes(),
            ])
            ->add('isAppointmentTaken', CheckboxType::class, [
                'label'    => 'Rendez vous pris ?',
                'required' => false,
                'mapped'   => false,
            ])
            ->add('referedCall', HiddenType::class)
        ;
    }

    public function getContactTypes()
    {
        $contactsTypes = $this->contactTypeRepository->findBy([], ['name'=>'ASC']);
        $choices = [];
        $choices['Type de contact'] = '';
        foreach ($contactsTypes as $contactType) {
            $choices[$contactType->getName()] = $contactType->getId();
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CallProcessing::class,
        ]);
    }
}
