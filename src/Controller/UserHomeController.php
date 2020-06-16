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
        // $appUser sera l'utilisateur connecté
        $appUser = $userRepository->findOneById(6);
        $callsForUser = $callRepository->callsToProcessByUser($appUser);
        return $this->render('user_home.html.twig', [
            'user' => $appUser,
            'calls' => $callsForUser
        ]);
    }
}
