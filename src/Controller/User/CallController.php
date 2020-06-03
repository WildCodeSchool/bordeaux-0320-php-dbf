<?php


namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ListController
 * @package App\Controller\User
 * @Route("/call",  name="call_")
 */

class CallController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     * @return Response
     */
    public function add(): Response
    {
        //TODO formulaire ajouter un appel
        return $this->render('User/call_add.html.twig', [
        ]);
    }
}
