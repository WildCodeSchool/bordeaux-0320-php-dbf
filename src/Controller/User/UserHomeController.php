<?php


namespace App\Controller\User;

use App\Repository\CallRepository;
use App\Repository\UserRepository;
use App\Service\ClientCallbacks;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserHomeController extends AbstractController
{
    const LAST_CALL_ID = 'lastCallId';
    const SESSION      = 'session';

    /**
     * @Route("/welcome/{id}", name="user_home")
     * @IsGranted("ROLE_USER")
     */
    public function homeCell(CallRepository $callRepository, UserRepository $userRepository, $id = null): Response
    {
        $appUser = $this->getUser();
        $header = false;
        if (null !== $id) {
            $appUser = $userRepository->findOneById($id);
            $service = $appUser->getService();
            $header = true;
            if (!$service->isServiceHead($this->getUser())) {
                $appUser = $this->getUser();
                $header = false;
            }
        }


        $callsToProcess = $callRepository->callsToProcessByUser($appUser);
        $callsToProcessForUserInService = $callRepository->callsToProcessByService($appUser->getService());
        $callsToProcess = array_merge($callsToProcessForUserInService, $callsToProcess);
        $totalToProcess = count($callsToProcess);
        $totalInProcess = count($callRepository->callsInProcessByUser($appUser));

        $lastCall = $callRepository->lastCallToProcessByUser($appUser);
        $this->get(self::SESSION)->set(self::LAST_CALL_ID, 0);
        if ($lastCall) {
            $this->get(self::SESSION)->set(self::LAST_CALL_ID, $lastCall->getId());
        }

        return $this->render('cell_home.html.twig', [
            'user' => $appUser,
            'calls' => $callsToProcess,
            'to_process' => $totalToProcess,
            'in_process' => $totalInProcess,
            'header' => $header,
        ]);
    }

    /**
     * @Route("/processing/{id}", name="user_calls_in_process")
     * @IsGranted("ROLE_USER")
     */
    public function homeProcessingCalls(
        CallRepository $callRepository,
        UserRepository $userRepository,
        $id = null
    ): Response {
        $appUser = $this->getUser();
        $header = false;
        if (!is_null($id)) {
            $appUser = $userRepository->findOneById($id);
            $service = $appUser->getService();
            $header = true;
            if (!$service->isServiceHead($this->getUser())) {
                $appUser = $this->getUser();
                $header = false;
            }
        }
        $callsToProcess = $callRepository->callsInProcessByUser($appUser);
        $totalToProcess = count($callRepository->callsToProcessByUser($appUser));
        $totalInProcess = count($callRepository->callsInProcessByUser($appUser));

        $lastCall = $callRepository->lastCallToProcessByUser($appUser);
        $this->get(self::SESSION)->set(self::LAST_CALL_ID, null);
        if ($lastCall) {
            $this->get(self::SESSION)->set(self::LAST_CALL_ID, $lastCall->getId());
        }

        return $this->render('cell_home.html.twig', [
            'user' => $appUser,
            'calls' => $callsToProcess,
            'to_process' => $totalToProcess,
            'in_process' => $totalInProcess,
            'header' => $header,
        ]);
    }


    /**
     * @Route("/newcallsforuser", name="user_new_call")
     * @IsGranted("ROLE_USER")
     */
    public function newCall(CallRepository $callRepository, UserRepository $userRepository)
    {
        $appUser = $this->getUser();
        $lastId = $this->get(self::SESSION)->get(self::LAST_CALL_ID);
        $newCalls = $callRepository->getNewCallsForUser($appUser, $lastId);
        if (!empty($newCalls)) {
            $this->get(self::SESSION)->set(self::LAST_CALL_ID, $newCalls[array_key_last($newCalls)]->getId());
            $response = $this->render('call_process/_new_calls.html.twig', [
                'calls' => $newCalls,
            ]);
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }
        return $response;
    }

    /**
     * @Route("/callbacksforuser", name="client_callbacks")
     * @IsGranted("ROLE_USER")
     */
    public function clientCallbacksForUser(
        CallRepository $callRepository,
        UserRepository $userRepository,
        ClientCallbacks $callbacks
    ) {
        $appUser = $this->getUser();
        $callsInProcess = $callRepository->callsInProcessByUser($appUser);
        $callsToProcess = $callRepository->callsToProcessByUser($appUser);
        $data = $callbacks::formatCallbacksData($callsInProcess, $callsToProcess);
        $response = new JsonResponse();
        $response->setData($data);
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}
