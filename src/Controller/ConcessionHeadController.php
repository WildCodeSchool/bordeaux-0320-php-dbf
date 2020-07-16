<?php

namespace App\Controller;

use App\Entity\ConcessionHead;
use App\Form\ConcessionHeadType;
use App\Repository\ConcessionHeadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/concession/head")
 */
class ConcessionHeadController extends AbstractController
{
    /**
     * @Route("/", name="concession_head_index", methods={"GET"})
     */
    public function index(ConcessionHeadRepository $concessionHeadRepository): Response
    {
        return $this->render('concession_head/index.html.twig', [
            'concession_heads' => $concessionHeadRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="concession_head_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $concessionHead = new ConcessionHead();
        $form = $this->createForm(ConcessionHeadType::class, $concessionHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($concessionHead);
            $entityManager->flush();

            return $this->redirectToRoute('concession_head_index');
        }

        return $this->render('concession_head/new.html.twig', [
            'concession_head' => $concessionHead,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="concession_head_show", methods={"GET"})
     */
    public function show(ConcessionHead $concessionHead): Response
    {
        return $this->render('concession_head/show.html.twig', [
            'concession_head' => $concessionHead,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="concession_head_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ConcessionHead $concessionHead): Response
    {
        $form = $this->createForm(ConcessionHeadType::class, $concessionHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('concession_head_index');
        }

        return $this->render('concession_head/edit.html.twig', [
            'concession_head' => $concessionHead,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="concession_head_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ConcessionHead $concessionHead): Response
    {
        if ($this->isCsrfTokenValid('delete'.$concessionHead->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($concessionHead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('concession_head_index');
    }
}
