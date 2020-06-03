<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \DateTime;

/**
 * @Route("/admin", name="admin")
 */
class AdminPanelController extends AbstractController
{

    /**
     * @Route("/", name="admin_dashboard")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $client = new Client();
        $formClient = $this->createForm(ClientType::class, $client);
        $formClient->handleRequest($request);

        if ($formClient->isSubmitted() && $formClient->isValid()) {
            if (!$client->getCreatedAt()) {
                $client->setCreatedAt(new DateTime());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();
            return $this->render('admin/index.html.twig', [
                'client' => $client,
                'form'   => $formClient->createView(),
                'result' => 'Client ajouté à la base de données'
            ]);

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/index.html.twig', [
            'client' => $client,
            'form' => $formClient->createView(),
        ]);
    }
}
