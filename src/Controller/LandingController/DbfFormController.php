<?php

namespace App\Controller\LandingController;

use App\Entity\Call;
use App\Events;
use App\Form\LandingForm\DbfBrandType;
use App\Form\LandingForm\DbfType;
use App\Service\Landing\Retardator;
use App\Service\Landing\Validator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use App\Service\Landing\OriginChecker;

/**
 * @Route("/dbf")
 */
class DbfFormController extends Retardator
{
    /**
     * @Route("/form/", name="landing_form_brand", methods={"GET"})
     * @param Request $request
     * @param null $brand
     * @return Response
     */
    public function choice(): Response
    {
        $landingForm = $this->createForm(DbfBrandType::class);

        return $this->render('landing/dbf_form_brand.html.twig', [
            'form' => $landingForm->createView(),
        ]);

    }

    /**
     * @Route("/form/{brand}", name="landing_form", methods={"GET", "POST"})
     * @param Request $request
     * @param Validator $validator
     * @param EventDispatcherInterface $eventDispatcher
     * @param null $brand
     * @return Response
     */
    public function index(Request $request, Validator $validator, EventDispatcherInterface $eventDispatcher, $brand): Response
    {
        $success = 0;
        $call = new Call();
        $landingForm = $this->createForm(DbfType::class, $call, [
            'brand' => $brand
        ]);

        $landingForm->handleRequest($request);


        $errors = [];

        if($landingForm->get('phone')->getData() && !$validator->isValidPhone($landingForm->get('phone')->getData())) {
            $landingForm->addError(new FormError('phoneError'));
            $errors['phone'] = 'Le numéro de téléphone est invalide';
        }

        if($landingForm->get('name')->getData() && !$validator->isValidName($landingForm->get('name')->getData())) {
            $landingForm->addError(new FormError('nameError'));
            $errors['name'] = 'Le nom ne doit comporter que des lettres';
        }

        if ($landingForm->get('callDate')->getData()){
            if (!$validator->isValidDay($landingForm->get('callDate')->getData())) {
                $landingForm->addError(new FormError('dateError'));
                $errors['day'] = 'Désolé, les rappels ne peuvent pas avoir lieu le week-end';
            }
        }

        if ($landingForm->isSubmitted() && $landingForm->isValid()) {
            $success = 1;
            $event = new GenericEvent($landingForm);
            $eventDispatcher->dispatch($event, Events::DBF_FORM_SUBMIT);

            $this->addFlash('landing_success', $validator->makeSuccessMessage($landingForm));

            return $this->render('landing/dbf_form.html.twig', [
                'form' => $landingForm->createView(),
                'errors' => $errors,
                'brand' => $brand,
                'success' => $success
            ]);

        }

        return $this->render('landing/dbf_form.html.twig', [
            'form' => $landingForm->createView(),
            'errors' => $errors,
            'brand' => $brand,
            'success' => $success
        ]);
    }




}
