<?php


namespace App\Controller;

use App\Repository\CallRepository;
use App\Repository\UserRepository;
use App\Service\CallOnTheWayDataMaker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserHomeController extends AbstractController
{

    /**
     * @Route("/welcome", name="user_home")
     * @IsGranted("ROLE_USER")
     */
    public function homeCell(CallRepository $callRepository, UserRepository $userRepository): Response
    {
        $appUser = $this->getUser();
        $callsToProcess = $callRepository->callsToProcessByUser($appUser);
        $totalToProcess = count($callRepository->callsToProcessByUser($appUser));
        $totalInProcess = count($callRepository->callsInProcessByUser($appUser));

        $lastCall = $callRepository->lastCallToProcessByUser($appUser);
        $this->get('session')->set('lastCallId', 0);
        if ($lastCall) {
            $this->get('session')->set('lastCallId', $lastCall->getId());
        }

        return $this->render('cell_home.html.twig', [
            'user'       => $appUser,
            'calls'      => $callsToProcess,
            'to_process' => $totalToProcess,
            'in_process' => $totalInProcess,
        ]);
    }

    /**
     * @Route("/processing", name="user_calls_in_process")
     * @IsGranted("ROLE_USER")
     */
    public function homeProcessingCalls(CallRepository $callRepository, UserRepository $userRepository): Response
    {
        $appUser = $this->getUser();
        $callsToProcess = $callRepository->callsInProcessByUser($appUser);
        $totalToProcess = count($callRepository->callsToProcessByUser($appUser));
        $totalInProcess = count($callRepository->callsInProcessByUser($appUser));

        $lastCall = $callRepository->lastCallToProcessByUser($appUser);
        $this->get('session')->set('lastCallId', null);
        if ($lastCall) {
            $this->get('session')->set('lastCallId', $lastCall->getId());
        }

        return $this->render('cell_home.html.twig', [
            'user'       => $appUser,
            'calls'      => $callsToProcess,
            'to_process' => $totalToProcess,
            'in_process' => $totalInProcess,
        ]);
    }



    /**
     * @Route("/newcallsforuser", name="user_new_call")
     * @IsGranted("ROLE_USER")
     */
    public function newCall(CallRepository $callRepository, UserRepository $userRepository)
    {
        $appUser = $this->getUser();
        $lastId  = $this->get('session')->get('lastCallId');
        $newCalls = $callRepository->getNewCallsForUser($appUser, $lastId);
        if (!empty($newCalls)) {
            $this->get('session')->set('lastCallId', $newCalls[array_key_last($newCalls)]->getId());
            $response = $this->render('call_process/_new_calls.html.twig', [
                'calls'            => $newCalls,
            ]);
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }
        return $response;
    }
}
