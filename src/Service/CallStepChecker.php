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

    const CALL_PROCESSING      = 'call_processing';
    const IS_APPOINTMENT_TAKEN = 'isAppointmentTaken';
    const APPOINTMENT_DATE     = 'appointmentDate';
    const CONTACT_TYPE         = 'contactType';

    public function __construct(ContactTypeRepository $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
        $this->contactType           = new ContactType();
    }


    public function checkAppointment(Request $request): bool
    {
        return (isset($request->request->get(self::CALL_PROCESSING)[self::IS_APPOINTMENT_TAKEN]) &&
            (int)$request->request->get(self::CALL_PROCESSING)[self::IS_APPOINTMENT_TAKEN] === 1);
    }

    public function checkAppointmentDate(Request $request): ?DateTime
    {
        return (
            isset($request->request->get(self::CALL_PROCESSING)[self::APPOINTMENT_DATE]) &&
            !empty($request->request->get(self::CALL_PROCESSING)[self::APPOINTMENT_DATE])
        ) ? new DateTime($request->request->get(self::CALL_PROCESSING)[self::APPOINTMENT_DATE]) : null;
    }

    public function isCallToBeEnded(Request $request): ?bool
    {
        $repo        = $this->contactTypeRepository;
        $contactType = $repo->findOneById((int)$request->request->get(self::CALL_PROCESSING)[self::CONTACT_TYPE]);
        $contactStep = $contactType->getIdentifier();
        return (
            (isset($request->request->get(self::CALL_PROCESSING)[self::IS_APPOINTMENT_TAKEN]) &&
            (int)$request->request->get(self::CALL_PROCESSING)[self::IS_APPOINTMENT_TAKEN] === 1) ||
            $contactStep === $this->contactType::ABANDON ||
            $contactStep === $this->contactType::NOT_ELIGIBLE ||
            $contactStep === $this->contactType::CONTACT
        );
    }
}
