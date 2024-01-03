<?php
namespace App\Service;

use App\Entity\Call;
use App\Repository\CallProcessingRepository;
use Symfony\Component\HttpFoundation\Response;

class ExportDataToCsv
{
    public function __construct(CallProcessingRepository $callProcessingRepository)
    {
        $this->processRepository = $callProcessingRepository;
    }



    /**
     * @param array $data
     * @return array
     */
    public function dataMakerBeforeExport(array $data): array
    {
        $dataReadyToExport = [];
        $dataReadyToExport[]=[
            'Date Appel',
            'Heure d\'appel',
            'Créateur',
            'Concession Créateur',
            'Plaque Destinataire',
            'Concession Destinataire',
            'Service Destinataire',
            'Motif',
            'Commentaire',
            'Dernier Destinataire',
            'Statut',
            'Dernier message laissé',
            'RDV',
            'origine',
        ];
        foreach ($data as $field) {

                $dataReadyToExport[] = [
                    $field->getCreatedAt()->format('d-m-Y'),
                    $field->getCreatedAt()->format('H:i'),
                    $field->getAuthor()->getFullName(),
                    $field->getAuthor()->getService()->getConcession()->getName(),
                    $field->getRecipient()->getService()->getConcession()->getTown()->getName(),
                    $field->getRecipient()->getService()->getConcession()->getName(),
                    (!is_null($field->getRecipient()->getService())) ?
                        $field->getRecipient()->getService()->getName() : '',
                    $field->getSubject()->getName(),
                    $field->getComment()->getName(),
                    $field->getRecipient()->getFullName(),
                    CallTreatmentDataMaker::getLastTreatment($field),
                    $this->processRepository->findLastProcessForCall($field->getId()) ? $this->processRepository->findLastProcessForCall($field->getId())->getComment() : '',
                    ($field->getIsAppointmentTaken()) ? 'oui' : 'non',
                    $field->getOrigin() ?? '',
                ];

        }
        return $dataReadyToExport;
    }
}
