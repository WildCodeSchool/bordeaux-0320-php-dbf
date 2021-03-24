<?php


namespace App\Controller\User;

use App\Entity\User;
use App\Repository\CallRepository;
use App\Repository\CityHeadRepository;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
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
    const LAST_CALL_TO_PROCESS_ID = 'lastCallToProcessId';
    const SESSION      = 'session';

    /**
     * @Route("/welcome/{id}", name="user_home")
     * @IsGranted("ROLE_USER")
     * @param ServiceRepository $serviceRepository
     * @param ServiceHeadRepository $serviceHeadRepository
     * @param ConcessionHeadRepository $concessionHeadRepository
     * @param CityHeadRepository $cityHeadRepository
     * @param CallRepository $callRepository
     * @param UserRepository $userRepository
     * @param null $id
     * @return Response
     */
    public function homeCell(
        ServiceRepository $serviceRepository,
        ServiceHeadRepository $serviceHeadRepository,
        ConcessionHeadRepository $concessionHeadRepository,
        CityHeadRepository $cityHeadRepository,
        CallRepository $callRepository, UserRepository
        $userRepository,
        $id = null
    ): Response {

        $appUser = $this->getUser();
        $service = $appUser->getService();

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
        $lastCallToProcess = $callRepository->lastCallToProcessByService($appUser->getService());
        $this->get(self::SESSION)->set(self::LAST_CALL_TO_PROCESS_ID, 0);

        if ($lastCall) {
            $this->get(self::SESSION)->set(self::LAST_CALL_ID, $lastCall->getId());
        }
        if ($lastCallToProcess) {
            $this->get(self::SESSION)->set(self::LAST_CALL_TO_PROCESS_ID, $lastCallToProcess->getId());
        }

        $isHead = false;
        $connectedUser = $this->getUser();
        if(
            $serviceHeadRepository->isServiceHead($connectedUser, $service) ||
            $concessionHeadRepository->isConcessionHead($connectedUser, $service->getConcession()) ||
            $cityHeadRepository->isCityHead($connectedUser, $service->getCOncession()->getTown())
        ) {
            $isHead = true;
        }

        return $this->render('cell_home.html.twig', [
            'connectedUser' => $connectedUser,
            'user' => $appUser,
            'calls' => $callsToProcess,
            'to_process' => $totalToProcess,
            'in_process' => $totalInProcess,
            'header' => $header,
            'isHead' => $isHead
        ]);
    }

    /**
     * @Route("/processing/{id}", name="user_calls_in_process")
     * @IsGranted("ROLE_USER")
     * @param CallRepository $callRepository
     * @param UserRepository $userRepository
     * @param ServiceHeadRepository $serviceHeadRepository
     * @param ConcessionHeadRepository $concessionHeadRepository
     * @param CityHeadRepository $cityHeadRepository
     * @param null $id
     * @return Response
     */
    public function homeProcessingCalls(
        CallRepository $callRepository,
        UserRepository $userRepository,
        ServiceHeadRepository $serviceHeadRepository,
        ConcessionHeadRepository $concessionHeadRepository,
        CityHeadRepository $cityHeadRepository,
        $id = null
    ): Response {
        $appUser = $this->getUser();
        $service = $appUser->getService();

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

        $isHead = false;
        $connectedUser = $this->getUser();
        if(
            $serviceHeadRepository->isServiceHead($connectedUser, $service) ||
            $concessionHeadRepository->isConcessionHead($connectedUser, $service->getConcession()) ||
            $cityHeadRepository->isCityHead($connectedUser, $service->getCOncession()->getTown())
        ) {
            $isHead = true;
        }

        $connectedUser = $this->getUser();
        return $this->render('cell_home.html.twig', [
            'user' => $appUser,
            'connectedUser' => $connectedUser,
            'calls' => $callsToProcess,
            'to_process' => $totalToProcess,
            'in_process' => $totalInProcess,
            'header' => $header,
            'isHead' => $isHead
        ]);
    }


    /**
     * @Route("/newcallsforuser/{user}", name="user_new_call")
     * @IsGranted("ROLE_USER")
     * @param CallRepository $callRepository
     * @param UserRepository $userRepository
     * @param User|null $user
     * @return JsonResponse|Response
     */
    public function newCall(CallRepository $callRepository, UserRepository $userRepository, ?User $user = null)
    {
        $appUser = ($user) ? $user : $this->getUser();
        $lastId = $this->get(self::SESSION)->get(self::LAST_CALL_ID);
        $lastIdToProcess = $this->get(self::SESSION)->get(self::LAST_CALL_TO_PROCESS_ID);

        $newCalls = $callRepository->getNewCallsForUser($appUser, $lastId);
        $newCallsToTake = $callRepository->newCallsToProcessByService($appUser->getService(), $lastIdToProcess);
        $totalCalls = array_merge($newCalls, $newCallsToTake);
        if (!empty($totalCalls)) {
            if(!empty($newCalls)) {
                $this->get(self::SESSION)->set(self::LAST_CALL_ID, $newCalls[array_key_last($newCalls)]->getId());
            }
            if(!empty($newCallsToTake)) {
                $this->get(self::SESSION)->set(self::LAST_CALL_TO_PROCESS_ID, $newCallsToTake[array_key_last($newCallsToTake)]->getId());
            }
            $response = $this->render('call_process/_new_calls.html.twig', [
                'calls' => $totalCalls,
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
