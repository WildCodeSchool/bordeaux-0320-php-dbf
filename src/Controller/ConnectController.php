<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConnectController extends AbstractController
{
    /**
     * @Route("/", name="connect")
     */
    public function index()
    {
        return $this->render('connect/index.html.twig');
    }
}
