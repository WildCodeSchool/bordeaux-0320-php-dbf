<?php


namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserHomeController extends AbstractController
{
    /**
     * @Route("/welcome", name="user_home")
     */
    public function homeUser(): Response
    {
        return $this->render('user_home.html.twig', [

        ]);
    }
}
