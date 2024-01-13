<?php

namespace App\Controller\User;

use App\Entity\Service;
use App\Repository\CallRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\UserRepository;
use App\Service\HeadBoardData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HeadBoardController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class HeadBoardController extends AbstractController
{
    /**
     * @Route("/head/board", name="head_board")
     * @param ServiceHeadRepository $serviceHeadRepository
     * @param UserRepository $userRepository
     * @param HeadBoardData $headBoardData
     * @param CallRepository $callRepository
     * @return Response
     */
    public function index(
        ServiceHeadRepository $serviceHeadRepository,
        UserRepository $userRepository,
        HeadBoardData $headBoardData,
        CallRepository $callRepository
    ) {
        $user             = $this->getUser();
        $res              = $serviceHeadRepository->getHeadServiceCalls($user);
        $dataForServices  = $headBoardData->makeDataForHead($res);
        $totalByService   = count($callRepository->callsToProcessByService($user->getService()));
        $totalToProcess   = count($callRepository->callsToProcessByUser($user));
        $totalInProcess   = count($callRepository->callsInProcessByUser($user));
        $callsAddedByUser = count($callRepository->getCallsAddedByUserToday($user));
        return $this->render('head_board/index.html.twig', [
            'cities'              => $dataForServices,
            'calls_in_process'    => $totalInProcess,
            'calls_to_process'    => $totalToProcess + $totalByService,
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


    /**
     * @Route("/head/supervision/{service}", name="head_board_supervision", methods={"GET"})
     */
    public function supervision(
        Service $service,
        ServiceHeadRepository $serviceHeadRepository,
        HeadBoardData $headBoardData
    ) {
        $user = $this->getUser();
        $res              = $serviceHeadRepository->getHeadServiceCalls($user);
        $dataForServices  = $headBoardData->makeDataForHead($res);
        $concession = $service->getConcession();
        $city = $concession->getTown();
        $serviceName = $service->getName();
        $concessionName = $concession->getName();
        $cityName = $city->getName();

        foreach ($dataForServices as $city => $data) {
            if($city === $cityName) {
                foreach ($data['concessions'] as $concession => $services) {
                    if($concession === $concessionName) {
                        foreach ($services['services'] as $service => $infos) {
                            if($service === $serviceName) {
                                $result = $infos;
                            }
                        }
                    }
                }
            }
        }

        return $this->render('call_process/__supervision.html.twig', [
            'service' => $result
        ]);
    }


}
