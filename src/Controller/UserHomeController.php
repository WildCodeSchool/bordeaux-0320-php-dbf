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
        $callsInProcess  = $callRepository->callsInProcessByUser($appUser);
        return $this->render('user_home.html.twig', [
            'user'             => $appUser,
            'calls'            => $callsToProcess,
            'calls_in_process' => $callsInProcess,
        ]);
    }
}
