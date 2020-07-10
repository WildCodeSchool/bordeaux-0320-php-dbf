<?php

namespace App\Controller;

use App\Repository\CallRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\UserRepository;
use App\Service\HeadBoardData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HeadBoardController
 * @package App\Controller
 */
class HeadBoardController extends AbstractController
{
    /**
     * @Route("/head/board", name="head_board")
     */
    public function index(
        ServiceHeadRepository $serviceHeadRepository,
        UserRepository $userRepository,
        HeadBoardData $headBoardData,
        CallRepository $callRepository
    ) {
        $user = $this->getUser();

        $headServices = $user->getServiceHeads();
        $dataForServices = $headBoardData->makeDataForHeads($headServices);
        $totalToProcess = count($callRepository->callsToProcessByUser($user));
        $totalInProcess = count($callRepository->callsInProcessByUser($user));
        $callsAddedByUser = count($callRepository->getCallsAddedByUser($user));

        return $this->render('head_board/index.html.twig', [
            'services' => $dataForServices,
            'calls_in_process' => $totalInProcess,
            'calls_to_process' => $totalToProcess,
            'calls_added_by_user' => $callsAddedByUser,
        ]);
    }
}
