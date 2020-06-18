<?php


namespace App\Controller;

use App\Repository\CallRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserHomeController extends AbstractController
{

    /**
     * @Route("/welcome", name="user_home")
     */
    public function homeUser(CallRepository $callRepository, UserRepository $userRepository): Response
    {
        $appUser = $this->getUser();
        $callsToProcess = $callRepository->callsToProcessByUser($appUser);
        $lastCall = $callRepository->lastCallToProcessByUser($appUser);
        $this->get('session')->set('lastCallId', $lastCall->getId());

        $callsInProcess  = $callRepository->callsInProcessByUser($appUser);
        return $this->render('user_home.html.twig', [
            'user'             => $appUser,
            'calls'            => $callsToProcess,
            'calls_in_process' => $callsInProcess,
        ]);
    }

    /**
     * @Route("/newcallsforuser", name="user_new_call")
     */
    public function newCall(CallRepository $callRepository, UserRepository $userRepository): Response
    {
        $appUser = $this->getUser();
        $lastId  = $this->get('session')->get('lastCallId');
        $newCalls = $callRepository->getNewCallsForUser($appUser, $lastId);
        if (!empty($newCalls)) {
            $this->get('session')->set('lastCallId', $newCalls[array_key_last($newCalls)]->getId());
        }
        return $this->render('call_process/_new_calls.html.twig', [
            'calls'            => $newCalls,
        ]);
    }
}
