<?php

namespace App\Controller\User;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\CallRepository;
use App\Service\ExportDataToCsv;
use App\Service\OnlyCallKeeper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_COLLABORATOR")
 * */
class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     * @param SearchData $searchData
     * @param Request $request
     * @param CallRepository $callRepository
     * @param ExportDataToCsv $exportDataToCsv
     * @param OnlyCallKeeper $onlyCallKeeper
     * @return Response
     */
    public function index(
        SearchData $searchData,
        Request $request,
        CallRepository $callRepository,
        ExportDataToCsv $exportDataToCsv,
        OnlyCallKeeper $onlyCallKeeper,
        KernelInterface $kernel
    ): Response {
        $searchedCalls = [];
        $dataReadyForExport='';

        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $searchedCalls = $onlyCallKeeper::keepCalls($callRepository->findSearch($form->getData()));
            $dataReadyForExport = array_map(function ($call) {
                return $call->getId();
            }, $searchedCalls);
            $dataReadyForExport = json_encode($dataReadyForExport);
        }
        return $this->render('search/index.html.twig', [
            'form'=> $form->createView(),
            'calls'=> $searchedCalls,
            'export'=> $dataReadyForExport,
            'filename' => 'build/exports/export' . $this->getUser()->getId() . '.csv',
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/export", name="export_to_csv", methods={"POST"})
     * @param ExportDataToCsv $exportDataToCsv
     * @return Response
     */
    public function exportToCSV(Request $request, CallRepository $callRepository, ExportDataToCsv $exportDataToCsv)
    {
        $callsIds = json_decode($request->request->get('calls'), true);
        $calls = $callRepository->findBy(['id' => $callsIds]);
        $fileName = uniqid();

        if (is_null($calls)) {
            $this->addFlash('error', 'Faites d\'abord une recherche');
            return $this->redirectToRoute('search');
        }
        $calls =  $exportDataToCsv->dataMakerBeforeExport($calls);

        $folder = sys_get_temp_dir();
        $fp = fopen($folder . '/' . $fileName . '.csv', 'w');

        foreach ($calls as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        $headers = [
            'Content-Type'     => 'application/CSV',
            'Content-Disposition' => 'attachment;filename="appels.csv"'
        ];

       $response = new BinaryFileResponse(
            $folder . '/' . $fileName . '.csv',
            Response::HTTP_OK,
            $headers,
            true,
        );
        return $response;
    }

}
