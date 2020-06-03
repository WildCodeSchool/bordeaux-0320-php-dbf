<?php


namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ListController
 * @package App\Controller\User
 * @Route("/calls" , name="calls_")
 */
class ListController extends AbstractController
{
    /**
     * @Route("/to-treat", name="to_treat")
     * @return Response
     */
    public function index(): Response
    {
        //TODO Appel a traiter
        return $this->render('User/calls_to_treat.html.twig', [
        ]);
    }
    /**
     * @Route("/treated", name="treated")
     * @return Response
     */

    public function show(): Response
    {
        //TODO Appels Traités
        return $this->render('User/calls_treated.html.twig', [
        ]);
    }
    /**
     * @Route("/handling", name="handling")
     * @return Response
     */

    public function handling(): Response
    {
        //TODO Gestion des appels
        return $this->render('User/calls_handling.html.twig', [
        ]);
    }


    /**
     * @Route("/all", name="all")
     * @return Response
     */
    public function all(): Response
    {
    //TODO tous les appels
        return $this->render('User/calls_all.html.twig', [
        ]);
    }
}
