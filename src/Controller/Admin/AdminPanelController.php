<?php

namespace App\Controller\Admin;

use App\Entity\Civility;
use App\Entity\Client;
use App\Entity\Service;
use App\Form\CivilityType;
use App\Form\ClientType;
use App\Form\ServiceType;
use App\Repository\CivilityRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Variable;
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
     * @Route("/", name="_dashboard")
     * @param Request $request
     * @param VehicleRepository $vehicleRepository
     * @return Response
     */
    public function index(Request $request, VehicleRepository $vehicleRepository): Response
    {
        $client = new Client();
        $civilities = $this->getDoctrine()->getRepository(Civility::class);
        $services = $this->getDoctrine()->getRepository(Service::class);
        $formClient = $this->createForm(ClientType::class, $client);
        $formClient->handleRequest($request);
        $formCivility = $this->createForm(CivilityType::class);
        $formService = $this->createForm(ServiceType::class);

        return $this->render('admin/index.html.twig', [
            'client' => $client,
            'form' => $formClient->createView(),
            'services'=> $services->findAll(),
            'form_civility' => $formCivility->createView(),
            'form_service' => $formService->createView(),
            'civilities' => $civilities->findAll(),
            'vehicles' => $vehicleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/addclient")
     * @param CivilityRepository $civilityRepository
     * @throws \Exception
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
        $client->setCreatedAt();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($client);
        $entityManager->flush();
    }
}
