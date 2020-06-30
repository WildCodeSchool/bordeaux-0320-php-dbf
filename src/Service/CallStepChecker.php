<?php


namespace App\Service;

use App\Entity\ContactType;
use App\Repository\ContactTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;

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
            (int)$request->request->get('call_processing')['isAppointmentTaken'] === 1
        ) {
            return true;
        }
        return false;
    }

    public function checkAppointmentDate(Request $request): ?DateTime
    {
        return (
            isset($request->request->get('call_processing')['appointmentDate']) &&
            !empty($request->request->get('call_processing')['appointmentDate'])
        ) ? new DateTime($request->request->get('call_processing')['appointmentDate']) : null;
    }

    public function isCallToBeEnded(Request $request): ?bool
    {
        $repo        = $this->contactTypeRepository;
        $contactType = $repo->findOneById((int)$request->request->get('call_processing')['contactType']);
        $contactStep = $contactType->getIdentifier();
        if ((isset($request->request->get('call_processing')['isAppointmentTaken']) &&
            (int)$request->request->get('call_processing')['isAppointmentTaken'] === 1) ||
            $contactStep === $this->contactType::ABANDON ||
            $contactStep === $this->contactType::NOT_ELIGIBLE ||
            $contactStep === $this->contactType::CONTACT
        ) {
            return true;
        }
        return false;
    }
}
