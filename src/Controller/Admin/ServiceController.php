<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\CallProcessingRepository;
use App\Repository\CallRepository;
use App\Repository\CallTransferRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\ServiceRepository;
use App\Service\Deletors\ServiceDeletor;
use App\Service\HeadManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/service")
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("/", name="service_index", methods={"GET"})
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="service_new", methods={"GET","POST"})
     * @param Request $request
     * @param HeadManager $headManager
     * @return Response
     */
    public function new(Request $request, HeadManager $headManager, ServiceRepository $serviceRepository): Response
    {
        $service = new Service();
        $formService = $this->createForm(ServiceType::class, $service);
        $formService->handleRequest($request);

        if ($formService->isSubmitted() && $formService->isValid()) {
            $existingService = $serviceRepository->findOneBy([
                        'name' => $formService->get('name')->getData(),
                        'concession' => $formService->get('concession')->getData(),
                    ]
            );

            if($existingService) {
                $this->addFlash('error', 'Un service avec le même nom existe déjà');
                return $this->redirectToRoute('admin_dashboard');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();

            $headManager->addServiceHeads($service);

            $this->addFlash('success', 'Vous avez bien ajouté un service');
        } else {
            $errors['name'] = $formService['name']->getErrors();
            $errors['concession'] = $formService['concession']->getErrors();
            foreach ($errors as $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $this->addFlash("error", $error->getMessage());
                }
            }
        }
        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/{id}", name="service_show", methods={"GET"})
     * @param Service $service
     * @return Response
     */
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="service_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Service $service
     * @return Response
     */
    public function edit(Request $request, Service $service): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form_service' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{service}", name="delete_service", methods={"DELETE"})
     * @param Service $service
     * @param ServiceDeletor $serviceDeletor
     * @return JsonResponse
     */
    public function delete(
        Service $service,
        ServiceDeletor $serviceDeletor
    ): JsonResponse {


            $entityManager = $this->getDoctrine()->getManager();

            $serviceDeletor->deleteService($service);

            $response = new JsonResponse();
            $status = JsonResponse::HTTP_NO_CONTENT;
            $response->setStatusCode($status);

        return $response;
    }
}
