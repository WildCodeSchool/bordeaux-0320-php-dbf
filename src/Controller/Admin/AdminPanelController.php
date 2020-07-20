<?php

namespace App\Controller\Admin;

use App\Entity\Civility;
use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Concession;
use App\Entity\Service;
use App\Entity\Subject;
use App\Form\CityType;
use App\Form\CivilityType;
use App\Form\ClientType;
use App\Form\CommentType;
use App\Form\ConcessionType;
use App\Form\ServiceType;
use App\Form\SubjectType;
use App\Repository\CityRepository;
use App\Repository\CivilityRepository;
use App\Repository\CommentRepository;
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
 * @IsGranted("ROLE_ADMIN")
 */
class AdminPanelController extends AbstractController
{

    /**
     * @Route("/", name="_dashboard")
     * @param Request $request
     * @param VehicleRepository $vehicleRepository
     * @param SubjectRepository $subjectRepository
     * @param CommentRepository $commentRepository
     * @param CityRepository $cityRepository
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function index(
        Request $request,
        VehicleRepository $vehicleRepository,
        SubjectRepository $subjectRepository,
        CommentRepository $commentRepository,
        CityRepository $cityRepository
    ): Response {

        $client = new Client();
        $civilities = $this->getDoctrine()->getRepository(Civility::class);
        $services = $this->getDoctrine()->getRepository(Service::class);
        $concessions = $this->getDoctrine()->getRepository(Concession::class);
        $formClient = $this->createForm(ClientType::class, $client);
        $formClient->handleRequest($request);
        $formCivility = $this->createForm(CivilityType::class);
        $formService = $this->createForm(ServiceType::class);
        $formConcession = $this->createForm(ConcessionType::class);
        $formSubject = $this->createForm(SubjectType::class);
        $formComment = $this->createForm(CommentType::class);
        $formCity   = $this->createForm((CityType::class));

        return $this->render('admin/index.html.twig', [
            'client'            => $client,
            'form'              => $formClient->createView(),
            'services'          => $services->findAll(),
            'form_civility'     => $formCivility->createView(),
            'form_service'      => $formService->createView(),
            'form_concession'   => $formConcession->createView(),
            'form_subject'      => $formSubject->createView(),
            'form_comment'      => $formComment->createView(),
            'form_city'         => $formCity->createView(),
            'civilities'        => $civilities->findAll(),
            'concessions'       => $concessions->findAll(),
            'vehicles'          => $vehicleRepository->findAll(),
            'subjects'          => $subjectRepository->findAll(),
            'comments'          => $commentRepository->findAll(),
            'cities'            => $cityRepository->findAll(),

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
        $client->setEmail($post['email']);
        $client->setCreatedAt();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($client);
        $entityManager->flush();
    }
}
