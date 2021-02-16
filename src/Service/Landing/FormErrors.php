<?php


namespace App\Service\Landing;


use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

class FormErrors
{

    public static function getErrors(Form $landingForm, Validator $validator, string $dateFieldName = 'callDate')
    {
        $errors = [];

        if ($landingForm->get('phone')->getData() && !$validator->isValidPhone($landingForm->get('phone')->getData())) {
            $landingForm->addError(new FormError('phoneError'));
            $errors['phone'] = 'Le numéro de téléphone est invalide';
        }

        if ($landingForm->get('name')->getData() && !$validator->isValidName($landingForm->get('name')->getData())) {
            $landingForm->addError(new FormError('nameError'));
            $errors['name'] = 'Le nom ne doit comporter que des lettres';
        }
        if ($landingForm->get($dateFieldName)->getData()) {
            if (!$validator->isValidDay($landingForm->get($dateFieldName)->getData())) {
                $landingForm->addError(new FormError('dateError'));
                $errors['day'] = 'Désolé, les rappels ne peuvent pas avoir lieu le week-end';
            }

        }

        return $errors;
    }

}
