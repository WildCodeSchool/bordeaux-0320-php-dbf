<?php

namespace App\Controller\LandingController;

use App\Entity\Call;
use App\Events;
use App\Form\LandingForm\CarrosserieType;
use App\Service\Landing\FormErrors;
use App\Service\Landing\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DbfNewLandingFormController extends AbstractController
{

    /**
     * @Route("/carrosserie/{city}/{color}", name="carrosserie_landing_form", methods={"GET", "POST"})
     * @param Request $request
     * @param Validator $validator
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $brand
     * @param string|null $referer
     * @return Response
     */
    public function index(
        Request $request,
        Validator $validator,
        EventDispatcherInterface $eventDispatcher,
        string $city,
        string $color
    ): Response {
        $success = 0;

        $landingForm = $this->createForm(CarrosserieType::class,  [
            'city' => $city,
        ]);

        $landingForm->handleRequest($request);


        $errors = FormErrors::getErrors($landingForm, $validator);

        if ($landingForm->isSubmitted() && $landingForm->isValid()) {
            $success = 1;
            $event = new GenericEvent($landingForm);
            $eventDispatcher->dispatch($event, Events::CARROSSERIE_FORM_SUBMIT);

            $this->addFlash('landing_success', $validator->makeSuccessMessage($landingForm));

            return $this->render('landing/dbf_carrosserie_form.html.twig', [
                'form' => $landingForm->createView(),
                'errors' => $errors,
                'city' => $city,
                'success' => $success,
                'color' => $color
            ]);
        }

        return $this->render('landing/dbf_carrosserie_form.html.twig', [
            'form' => $landingForm->createView(),
            'errors' => $errors,
            'city' => $city,
            'success' => $success,
            'color' => $color
        ]);
    }

}