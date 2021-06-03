<?php

namespace App\Controller\User;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\CallRepository;
use App\Service\ExportDataToCsv;
use App\Service\OnlyCallKeeper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $dataReadyForExport = json_encode($exportDataToCsv->dataMakerBeforeExport($searchedCalls));

            // Making CSV File
            $folder = $kernel->getProjectDir() . '/public/build/exports';
            if(!is_dir($folder)) {
                mkdir($folder);
            }
            $fp = fopen($folder . '/export' . $this->getUser()->getId() . '.csv', 'w');
            foreach ($exportDataToCsv->dataMakerBeforeExport($searchedCalls) as $row) {
                $row = str_replace(['à', 'é', 'è', 'ç', 'ô', 'û', 'ù', 'ö', 'ü', 'ï', 'ê'], ['a', 'e', 'e', 'c', 'o', 'u', 'u', 'o', 'u', 'i', 'e'], $row);
                //fputs($fp, (chr(0xEF) . chr(0xBB) . chr(0xBF)));
                fputcsv($fp, $row, ';', '"');
            }
            fclose($fp);


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
     * @Route("/export/{exportedCalls}", name="export_to_csv", requirements={"exportedCalls"=".+"})
     * @param ExportDataToCsv $exportDataToCsv
     * @param string $exportedCalls
     * @return Response
     */
    public function exportToCSV(ExportDataToCsv $exportDataToCsv, string $exportedCalls = null)
    {
        $searchedCalls = json_decode($exportedCalls, true);
        if (is_null($searchedCalls)) {
            $this->addFlash('error', 'Faites d\'abord une recherche');
            return $this->redirectToRoute('search');
        }
        return $exportDataToCsv->exportDataToCsv($searchedCalls, 'export_' . $this->getUser()->getId());
    }
}
