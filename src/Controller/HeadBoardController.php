<?php

namespace App\Controller;

use App\Repository\CallRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\UserRepository;
use App\Service\HeadBoardData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
        $res = $serviceHeadRepository->getHeadServiceCalls($user);
        $dataForServices = $headBoardData->makeDataForHead($res);
        $totalToProcess = count($callRepository->callsToProcessByUser($user));
        $totalInProcess = count($callRepository->callsInProcessByUser($user));
        $callsAddedByUser = count($callRepository->getCallsAddedByUserToday($user));

        return $this->render('head_board/index.html.twig', [
            'cities' => $dataForServices,
            'calls_in_process' => $totalInProcess,
            'calls_to_process' => $totalToProcess,
            'calls_added_by_user' => $callsAddedByUser,
        ]);
    }

    /**
     * @Route("/head/data", name="head_board_data", methods={"GET"})
     */
    public function getHeadBoardData(
        ServiceHeadRepository $serviceHeadRepository,
        HeadBoardData $headBoardData
    ) {
        $user = $this->getUser();
        $res = $serviceHeadRepository->getHeadServiceCalls($user);
        $data = $headBoardData->makeDataForHeadUpdater($res);
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_OK);
        if (is_null($data)) {
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
        }
        $response->setData($data);
        return $response;
    }
}
