<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin")
 */
class AdminPanelController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [

        ]);
    }
}
