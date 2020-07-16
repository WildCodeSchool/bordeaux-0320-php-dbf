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
     * @Route("/{id}/edit", name="city_head_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CityHead $cityHead): Response
    {
        $form = $this->createForm(CityHeadType::class, $cityHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('service_head_index');
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

        return $this->redirectToRoute('service_head_index');
    }
}
