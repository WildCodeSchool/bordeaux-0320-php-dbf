<?php

namespace App\Controller\LandingController;

use App\Entity\Call;
use App\Form\LandingForm\LandingType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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
        $landingForm = $this->createForm(LandingType::class, $call, [
            'brand' => $brand
        ]);

        $landingForm->handleRequest($request);


        $errors = [];

        if($landingForm->get('phone')->getData() && !$this->isValidPhone($landingForm->get('phone')->getData())) {
            $landingForm->addError(new FormError('phoneError'));
            $errors['phone'] = 'Le numéro de téléphone est invalide';
        }

        if($landingForm->get('name')->getData() && !$this->isValidName($landingForm->get('name')->getData())) {
            $landingForm->addError(new FormError('nameError'));
            $errors['name'] = 'Le nom ne doit comporter que des lettres';
        }

        if($landingForm->get('immatriculation')->getData() && !$this->isValidImmat($landingForm->get('immatriculation')->getData())) {
            $landingForm->addError(new FormError('immatError'));
            $errors['immat'] = 'Votre immatriculation n\'est pas conforme : AA-555-BB';
        }

        if ($landingForm->get('callDate')->getData()){
            if (!$this->isValidDay($landingForm->get('callDate')->getData())) {
                $landingForm->addError(new FormError('dateError'));
                $errors['day'] = 'Désolé, les rappels ne peuvent pas avoir lieu le week-end';
            }
        }

        if ($landingForm->isSubmitted() && $landingForm->isValid()) {
            dd($request->server->get('HTTP_REFERER'));

            $this->addFlash('landing_success', $this->makeSuccessMessage($landingForm));
            return $this->redirectToRoute('landing_form', [
            ]);
        }

        $domain = "dbf-autos.fr";
        $ip = gethostbyname($domain);

        return $this->render('landing/form_landing.html.twig', [
            'form' => $landingForm->createView(),
            'errors' => $errors,
            'brand' => $brand,
            'ipRemote'    => $ip
        ]);
    }


    private function getIp(): string
    {
        $ip = '';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    private function isValidName($name)
    {
        return ctype_alpha($name);
    }

     private function isValidImmat($name)
    {
        return preg_match("#[A-Za-z]{2,3}[-][0-9]{3}[-][A-Za-z]{2,3}# ", $name);
    }

    private function isValidDay(\DateTime $date)
    {
        $day = $date->format('N');
        return $day < 6;
    }

    private function isValidDate(\DateTime $date, $time)
    {
        $date = new \DateTime($date->format('Y-m-d') . 'T' . $time);
        $today = new \DateTime('now');
        $today->add(new \DateInterval('PT3H'));
        return $date > $today;
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
