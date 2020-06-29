<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/city")
 */
class CityController extends AbstractController
{
    /**
     * @Route("/", name="city_index", methods={"GET"})
     * @param CityRepository $cityRepository
     * @return Response
     */
    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="city_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $city = new City();
        $formCity = $this->createForm(CityType::class, $city);
        $formCity->handleRequest($request);

        if ($formCity->isSubmitted() && $formCity->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('city/new.html.twig', [
            'city' => $city,
            'form-city' => $formCity->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="city_show", methods={"GET"})
     * @param City $city
     * @return Response
     */
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="city_edit", methods={"GET","POST"})
     * @param Request $request
     * @param City $city
     * @return Response
     */
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form_city' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_city", methods={"DELETE"})
     * @return JsonResponse
     */

    public function delete(Request $request, City $city): JsonResponse
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($city);
        $entityManager->flush();

        $response = new JsonResponse();
        $status = JsonResponse::HTTP_OK;
        $response->setStatusCode($status);

        return $response;
    }
}
