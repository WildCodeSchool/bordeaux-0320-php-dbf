<?php


namespace App\Service;

use App\Entity\ContactType;
use App\Repository\ContactTypeRepository;
use Symfony\Component\HttpFoundation\Request;

class CallStepChecker
{
    private $contactTypeRepository;
    private $contactType;

    public function __construct(ContactTypeRepository $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
        $this->contactType           = new ContactType();
    }


    public function checkAppointment(Request $request): bool
    {
        if (isset($request->request->get('call_processing')['isAppointmentTaken']) &&
            (int)$request->request->get('call_processing')['isAppointmentTaken'] === 1) {
            return true;
        }
        return false;
    }

    public function isCallToBeEnded(Request $request): ?bool
    {
        $repo = $this->contactTypeRepository;
        $contactType = $repo->findOneById($request->request->get('call_processing')['contactType']);
        $contactStep = $contactType->getIdentifier();

        if ((isset($request->request->get('call_processing')['isAppointmentTaken']) &&
            (int)$request->request->get('call_processing')['isAppointmentTaken'] === 1) ||
            $contactStep === $this->contactType::ABANDON ||
            $contactStep === $this->contactType::NOT_ELIGIBLE ||
            $contactStep === $this->contactType::CONTACT) {
            return true;
        }
        return null;
    }
}
