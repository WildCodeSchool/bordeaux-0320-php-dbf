<?php

namespace App\Form;

use App\Entity\Call;
use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CallTransferType extends AbstractType
{
    private $cityRepository;
    private $concessionRepository;
    private $serviceRepository;
    private $userRepository;

    public function __construct(
        CityRepository $cityRepository,
        ConcessionRepository $concessionRepository,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository
    ) {
        $this->cityRepository       = $cityRepository;
        $this->concessionRepository = $concessionRepository;
        $this->serviceRepository    = $serviceRepository;
        $this->userRepository       = $userRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $call = $builder->getData();

        $builder
            ->add('city', EntityType::class, [
                'class'         => City::class,
                'choice_label'  => 'name',
                'mapped'        => false
            ])
            ->add('concession', ChoiceType::class, [
                'choices' => $this->getConcessions($call->getCityTransfer()),
                'data'    => $call->getConcession()->getId(),
                'mapped'  => false
            ])
            ->add('service', ChoiceType::class, [
                'choices' => $this->getServices($call->getConcessionTransfer()),
                'data'    => $call->getService()->getId(),
                'mapped'  => false
            ])
            ->add('recipient', ChoiceType::class, [
                'choices' => $this->getRecipients(),
                'data'    => $call->getRecipient()->getId(),
                'mapped'  => false
            ])
            ->add('comment', TextType::class, [
                'label' => 'commentaire',
                'required'   => false,
                'mapped'  => false
            ])
        ;
    }


    public function getConcessions($cityId = null)
    {
        if (!$cityId) {
            $concessions = $this->concessionRepository->findAll();
        } else {
            $concessions = $this->concessionRepository->findBy(['town' => $cityId]);
        }
        $choices = [];
        $choices['Choisir une concession'] = '';
        foreach ($concessions as $concession) {
            $choices[$concession->getName()] = $concession->getId();
        }
        return $choices;
    }


    public function getServices($concessionId = null)
    {
        if (is_null($concessionId)) {
            $services = $this->serviceRepository->findAll();
        } else {
            $services = $this->serviceRepository->findBy(['concession' => $concessionId]);
        }
        $choices = [];
        $choices['Choisir un service'] = '';
        foreach ($services as $service) {
            $choices[$service->getName().$service->getId()] = $service->getId();
        }

        return $choices;
    }



    public function getRecipients($serviceId = null)
    {
        if (is_null($serviceId)) {
            $recipients = $this->userRepository->findAll();
        } else {
            $recipients = $this->userRepository->findBy(['service' => $serviceId]);
        }
        $choices = [];
        $choices['Choisir un destinataire'] = '';
        foreach ($recipients as $user) {
            $choices[$user->getFirstname() . ' ' . $user->getLastname()] = $user->getId();
        }
        return $choices;
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Call::class,
            'allow_extra_fields' => true,
        ]);
    }
}
