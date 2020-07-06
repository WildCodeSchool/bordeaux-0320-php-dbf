<?php

namespace App\Controller\User;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\CallRepository;
use App\Service\ExportDataToCsv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     * @param SearchData $searchData
     * @param Request $request
     * @param CallRepository $callRepository
     * @param ExportDataToCsv $exportDataToCsv
     * @return Response
     */
    public function index(
        SearchData $searchData,
        Request $request,
        CallRepository $callRepository,
        ExportDataToCsv $exportDataToCsv
    ): Response {
        $searchedCalls = [];
        $dataReadyForExport='';
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchedCalls = $callRepository->findSearch($searchData);
            $dataReadyForExport = json_encode($exportDataToCsv->dataMakerBeforeExport($searchedCalls));
        }

        return $this->render('search/index.html.twig', [
            'form'=> $form->createView(),
            'calls'=> $searchedCalls,
            'export'=> $dataReadyForExport,
        ]);
    }

    /**
     * @Route("/export/{exportedCalls}", name="export_to_csv")
     * @param ExportDataToCsv $exportDataToCsv
     * @return Response
     */
    public function exportToCSV(ExportDataToCsv $exportDataToCsv, $exportedCalls = null)
    {

        $searchedCalls = json_decode($exportedCalls, true);
        $response = $exportDataToCsv->exportDataToCsv($searchedCalls, 'export_' . $this->getUser()->getId());

        return $response;
    }
}
