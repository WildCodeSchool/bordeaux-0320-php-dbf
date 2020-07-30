<?php

namespace App\Controller\Admin;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vehicle")
 * @IsGranted("ROLE_ADMIN")
 */
class VehicleController extends AbstractController
{
    const VEHICLE_INDEX = 'vehicle_index';
    /**
     * @Route("/", name="vehicle_index", methods={"GET"})
     * @param VehicleRepository $vehicleRepository
     * @return Response
     */
    public function index(VehicleRepository $vehicleRepository): Response
    {
        return $this->render('vehicle/index.html.twig', [
            'vehicles' => $vehicleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="vehicle_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($vehicle);
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez bien ajouté un véhicule');

            return $this->redirectToRoute(self::VEHICLE_INDEX);
        }

        return $this->render('vehicle/new.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vehicle_show", methods={"GET"})
     * @param Vehicle $vehicle
     * @return Response
     */
    public function show(Vehicle $vehicle): Response
    {
        return $this->render('vehicle/show.html.twig', [
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vehicle_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Vehicle $vehicle
     * @return Response
     */
    public function edit(Request $request, Vehicle $vehicle): Response
    {
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(self::VEHICLE_INDEX);
        }

        return $this->render('vehicle/edit.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vehicle_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Vehicle $vehicle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vehicle);
            $entityManager->flush();
        }

        return $this->redirectToRoute(self::VEHICLE_INDEX);
    }
}
