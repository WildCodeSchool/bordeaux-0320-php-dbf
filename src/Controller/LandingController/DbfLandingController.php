<?php

namespace App\Controller\LandingController;

use App\Entity\Call;
use App\Events;
use App\Form\LandingForm\DbfType;
use App\Form\LandingForm\LandingType;
use App\Repository\CityRepository;
use App\Service\Landing\FormErrors;
use App\Service\Landing\Redirector;
use App\Service\Landing\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class DbfLandingController extends AbstractController
{
    /**
     * @Route("/dbf/landing/{brand}/{city}/{reason}/{referer}", name="dbf_landing")
     * @param string $brand
     * @param string $city
     * @param string $reason
     * @param CityRepository $cityRepository
     * @param Request $request
     * @param Validator $validator
     * @param EventDispatcherInterface $eventDispatcher
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(
        string $brand,
        string $city,
        string $reason,
        string $referer = null,
        CityRepository $cityRepository,
        Request $request,
        Validator $validator,
        EventDispatcherInterface $eventDispatcher
    ) {

        $city = $cityRepository->findOneByName($city);

        if('PHONECITY' === $city->getIdentifier()) {
            return new RedirectResponse('http://dbf-autos.fr');
        }



        $call = new Call();
        $landingForm = $this->createForm(LandingType::class, $call, [
            'brand'  => $brand,
            'city'   => $city,
            'reason' => $reason,
            'referer' => $referer
        ]);

        $landingForm->handleRequest($request);

        $success = 0;

       $errors = FormErrors::getErrors($landingForm, $validator);

        if ($landingForm->isSubmitted() && $landingForm->isValid()) {
            $success = 1;
            $event = new GenericEvent($landingForm);
            $eventDispatcher->dispatch($event, Events::LANDING_FORM_SUBMIT);

            $this->addFlash('landing_success', $validator->makeSuccessMessage($landingForm));

            return $this->render('dbf_landing/index.html.twig', [
                'form' => $landingForm->createView(),
                'errors' => $errors,
                'brand' => $brand,
                'success' => $success,
                'city' => $city,
                'reason' => $reason
            ]);

        }

        return $this->render('dbf_landing/index.html.twig', [
            'form' => $landingForm->createView(),
            'errors' => $errors,
            'brand' => $brand,
            'success' => $success,
            'city' => $city,
            'reason' => $reason
        ]);

    }
}
