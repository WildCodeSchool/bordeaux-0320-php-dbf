<?php

namespace App\Controller;

use App\Entity\CityHead;
use App\Form\CityHeadType;
use App\Repository\CityHeadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/city/head")
 */
class CityHeadController extends AbstractController
{
    /**
     * @Route("/", name="city_head_index", methods={"GET"})
     */
    public function index(CityHeadRepository $cityHeadRepository): Response
    {
        return $this->render('city_head/index.html.twig', [
            'city_heads' => $cityHeadRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="city_head_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $cityHead = new CityHead();
        $formHeadCity = $this->createForm(CityHeadType::class, $cityHead);
        $formHeadCity->handleRequest($request);

        if ($formHeadCity->isSubmitted() && $formHeadCity->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cityHead);
            $entityManager->flush();

            return $this->redirectToRoute('city_head_index');
        }

        return $this->render('city_head/new.html.twig', [
            'city_head' => $cityHead,
            'form_head_city' => $formHeadCity->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="city_head_show", methods={"GET"})
     */
    public function show(CityHead $cityHead): Response
    {
        return $this->render('city_head/show.html.twig', [
            'city_head' => $cityHead,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="city_head_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CityHead $cityHead): Response
    {
        $form = $this->createForm(CityHeadType::class, $cityHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('city_head_index');
        }

        return $this->render('city_head/edit.html.twig', [
            'city_head' => $cityHead,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="city_head_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CityHead $cityHead): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cityHead->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cityHead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('city_head_index');
    }
}
