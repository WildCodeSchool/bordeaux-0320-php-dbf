<?php

namespace App\Controller\LandingController;

use App\Entity\Call;
use App\Entity\DbfContact;
use App\Events;
use App\Form\LandingForm\DbfBrandType;
use App\Form\LandingForm\DbfContactType;
use App\Form\LandingForm\DbfType;
use App\Service\Landing\FormErrors;
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
class DbfContactFormController extends Retardator
{

    /**
     * @Route("/contact/{referer}", name="dbf_contact_form", methods={"GET", "POST"})
     * @param Request $request
     * @param Validator $validator
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function index(
        Request $request,
        Validator $validator,
        EventDispatcherInterface $eventDispatcher,
        string $referer = null
    ): Response {
        $success = 0;
        $contact = new DbfContact();

        $landingForm = $this->createForm(DbfContactType::class, $contact, [
            'referer' => $referer
        ]);

        $landingForm->handleRequest($request);

        $errors = FormErrors::getErrors($landingForm, $validator, 'date');

        if ($landingForm->isSubmitted() && $landingForm->isValid()) {
            $success = 1;

            $event = new GenericEvent($landingForm);
            $eventDispatcher->dispatch($event, Events::DBF_CONTACT_FORM_SUBMIT);

            $this->addFlash('landing_success', $validator->makeContactSuccessMessage($landingForm));

            return $this->redirectToRoute('dbf_contact_form', ['referer' => $referer]);
        }

        return $this->render('landing/dbf_contact_form.html.twig', [
            'form' => $landingForm->createView(),
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
