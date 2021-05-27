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
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @return Response
     */
    public function exportDataToCsv(array $data, $filename, $delimiter = ';', $enclosure = '"')
    {
        $openFile = fopen("php://output", 'w');
        fputs($openFile, (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($openFile, array_values($data[0]), $delimiter, $enclosure);

        for ($i = 1; $i<count($data); $i++) {
            fputcsv($openFile, array_values($data[$i]), $delimiter, $enclosure);
        }

        fclose($openFile);
        $response= new Response();
        $response->headers->set('Content-disposition', 'attachment; filename="' . $filename . '.csv"');
        $response->headers->set('Content-type', 'text/csv');

        return $response;
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
                    $field->getCreatedAt()->format('d-m-Y H:i'),
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
