<?php


namespace App\Controller;

use App\Repository\CallRepository;
use App\Repository\UserRepository;
use App\Service\CallOnTheWayDataMaker;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserHomeController extends AbstractController
{
    /**
     * @Route("/welcome", name="user_home")
     */
    public function homeCell(CallRepository $callRepository, UserRepository $userRepository): Response
    {
        $appUser = $this->getUser();
        $callsToProcess = $callRepository->allCallsByUser($appUser);

        $lastCall = $callRepository->lastCallToProcessByUser($appUser);
        $this->get('session')->set('lastCallId', 0);
        if ($lastCall) {
            $this->get('session')->set('lastCallId', $lastCall->getId());
        }

        return $this->render('cell_home.html.twig', [
            'user'             => $appUser,
            'calls'            => $callsToProcess,
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
