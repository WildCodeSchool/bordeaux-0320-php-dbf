<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\CivilityRepository;
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


        return $this->render('admin/index.html.twig', [
            'client' => $client,
            'form' => $formClient->createView(),
        ]);
    }

    /**
     * @Route("/addclient", name="admin_addclient")
     */
    public function addClient(CivilityRepository $civilityRepository)
    {
        $post = file_get_contents('php://input');
        if ($post) {
            $post = json_decode($post, true);
        }
        $client = new Client();
        $civility=$civilityRepository->findOneBy(['id' => $post['civility']]);
        $client->setCivility($civility);
        $client->setName($post['name']);
        $client->setPhone($post['phone']);
        $client->setPhone2($post['phone2']);
        $client->setPostcode($post['postcode']);
        $client->setEmail($post['email']);
        $client->setCreatedAt(new DateTime());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($client);
        $entityManager->flush();
    }
}
