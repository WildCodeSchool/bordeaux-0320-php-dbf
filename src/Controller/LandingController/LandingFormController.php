<?php

namespace App\Controller\LandingController;

use App\Entity\Call;
use App\Form\LandingForm\LandingType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/landing")
 */
class LandingFormController extends AbstractController
{
    /**
     * @Route("/form/{brand}", name="landing_form", methods={"GET", "POST"})
     */
    public function index(Request $request, $brand = 'audi'): Response
    {

        $call = new Call();
        $landingForm = $this->createForm(LandingType::class, $call);

        $landingForm->handleRequest($request);

        if($landingForm->get('phone')->getData() && !$this->isValidPhone($landingForm->get('phone')->getData())) {
            $landingForm->addError(new FormError('phoneError'));
            $this->addFlash('landing_error', 'Le numéro de téléphone est invalide');
        }

        if($landingForm->get('name')->getData() && !$this->isValidName($landingForm->get('name')->getData())) {
            $landingForm->addError(new FormError('nameError'));
            $this->addFlash('landing_error', 'Le nom ne doit comporter que des lettres');
        }

        if($landingForm->get('immatriculation')->getData() && !$this->isValidImmat($landingForm->get('immatriculation')->getData())) {
            $landingForm->addError(new FormError('immatError'));
            $this->addFlash('landing_error', 'Votre immatriculation n\'est pas conforme : AA-555-BB');
        }

        if($landingForm->get('callDate')->getData() && !$this->isValidDate($landingForm->get('callDate')->getData())) {
            $landingForm->addError(new FormError('dateError'));
            $this->addFlash('landing_error', 'Désolé, les rappels ne peuvent pas avoir lieu le week-end');
        }

        if ($landingForm->isSubmitted() && $landingForm->isValid()) {

            $this->addFlash('landing_success', $this->makeSuccessMessage($landingForm));
            return $this->redirectToRoute('landing_form', [
            ]);
        }

        return $this->render('landing/form_landing.html.twig', [
            'form' => $landingForm->createView(),
            'brand' => $brand
        ]);
    }


    private function isValidName($name)
    {
        return preg_match("#[a-zA-Z]# ", $name);
    }

     private function isValidImmat($name)
    {
        return preg_match("#[A-Za-z]{2,3}[-][0-9]{3}[-][A-Za-z]{2,3}# ", $name);
    }

    private function isValidDate(\DateTime $date)
    {
        $day = $date->format('N');
        return $day < 6;
    }

    private function isValidPhone($phone)
    {
        $phone = str_replace(' ', '', $phone);
        return preg_match("#0[0-9]{9}# ", $phone);
    }

    private function makeSuccessMessage($form)
    {
        $m = $form->get('callMinutes')->getData() > 0 ? $form->get('callMinutes')->getData() : '';
        $message = '<span class="bolder">Merci ' . $form->get('civility')->getData()->getName() . ' ' . $form->get('name')->getData() . '</span><br>';
        $message .= 'Nous ferons notre possible pour vous rappeler le ' . $form->get('callDate')->getData()->format('d-m-Y') . ' aux alentours de ' .
            $form->get('callHour')->getData() . 'h' . $m . ' au ' . $form->get('phone')->getData();
        return $message;
    }


}
