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
                    $this->replace($field->getCreatedAt()->format('H:i')),
                    $this->replace($field->getAuthor()->getFullName()),
                    $this->replace($field->getAuthor()->getService()->getConcession()->getName()),
                    $this->replace($field->getRecipient()->getService()->getConcession()->getTown()->getName()),
                    $this->replace($field->getRecipient()->getService()->getConcession()->getName()),
                    (!is_null($field->getRecipient()->getService())) ?
                        $this->replace($field->getRecipient()->getService()->getName()) : '',
                    $this->replace($field->getSubject()->getName()),
                    $this->replace($field->getComment()->getName()),
                    $this->replace($field->getRecipient()->getFullName()),
                    $this->replace(CallTreatmentDataMaker::getLastTreatment($field)),
                    $this->replace($this->processRepository->findLastProcessForCall($field->getId()) ? $this->processRepository->findLastProcessForCall($field->getId())->getComment() : ''),
                    ($field->getIsAppointmentTaken()) ? 'oui' : 'non',
                    $field->getOrigin() ? $this->replace($field->getOrigin()) : '',
                ];

        }
        return $dataReadyToExport;
    }

    private function replace(?string $text)
    {
        if(!$text) {
            return '';
        }
        $text = str_replace(['à', 'é', 'è', 'ç', 'ô', 'û', 'ù', 'ö', 'ü', 'ï', 'ê'], ['a', 'e', 'e', 'c', 'o', 'u', 'u', 'o', 'u', 'i', 'e'], $text);
        return $text;

    }
}
