<?php

namespace App\Controller\Admin;

use App\Entity\Civility;
use App\Entity\Client;
use App\Entity\Concession;
use App\Entity\Service;
use App\Entity\Subject;
use App\Form\CivilityType;
use App\Form\ClientType;
use App\Form\ConcessionType;
use App\Form\ServiceType;
use App\Form\SubjectType;
use App\Repository\CivilityRepository;
use App\Repository\SubjectRepository;
use App\Repository\VehicleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Variable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \DateTime;

/**
 * @Route("/admin", name="admin")
 *
 */
class AdminPanelController extends AbstractController
{

    /**
     * @Route("/", name="_dashboard")
     * @param Request $request
     * @param VehicleRepository $vehicleRepository
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function index(
        Request $request, VehicleRepository $vehicleRepository): Response
    {
        $client = new Client();
        $civilities = $this->getDoctrine()->getRepository(Civility::class);
        $services = $this->getDoctrine()->getRepository(Service::class);
        $concessions = $this->getDoctrine()->getRepository(Concession::class);
        $subjects = $this->getDoctrine()->getRepository(Subject::class);
        $formClient = $this->createForm(ClientType::class, $client);
        $formClient->handleRequest($request);
        $formCivility = $this->createForm(CivilityType::class);
        $formService = $this->createForm(ServiceType::class);
        $formConcession = $this->createForm(ConcessionType::class);
        $formSubject = $this->createForm(SubjectType::class);

        return $this->render('admin/index.html.twig', [
            'client' => $client,
            'form' => $formClient->createView(),
            'services'=> $services->findAll(),
            'form_civility' => $formCivility->createView(),
            'form_service' => $formService->createView(),
            'form_concession'=> $formConcession->createView(),
            'form_subject' => $formSubject->createView(),
            'civilities' => $civilities->findAll(),
            'concessions'=> $concessions->findAll(),
            'subjects' => $subjects->findAll(),
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
