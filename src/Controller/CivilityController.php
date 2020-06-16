<?php

namespace App\Controller;

use App\Entity\Civility;
use App\Form\CivilityType;
use App\Repository\CivilityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/civility")
 * @IsGranted("ROLE_ADMIN")
 */
class CivilityController extends AbstractController
{
    /**
     * @Route("/", name="civility_index", methods={"GET"})
     */
    public function index(CivilityRepository $civilityRepository): Response
    {
        return $this->render('civility/index.html.twig', [
            'civilities' => $civilityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="civility_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $civility = new Civility();
        $formCivility = $this->createForm(CivilityType::class, $civility);
        $formCivility->handleRequest($request);

        if ($formCivility->isSubmitted() && $formCivility->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($civility);
            $entityManager->flush();
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('civility/new.html.twig', [
            'civility' => $civility,
            'form_civility' => $formCivility->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="civility_show", methods={"GET"})
     */
    public function show(Civility $civility): Response
    {
        return $this->render('civility/show.html.twig', [
            'civility' => $civility,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="civility_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Civility $civility): Response
    {
        $form = $this->createForm(CivilityType::class, $civility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('civility_index');
        }

        return $this->render('civility/edit.html.twig', [
            'civility' => $civility,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="civility_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Civility $civility): Response
    {
        if ($this->isCsrfTokenValid('delete' . $civility->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($civility);
            $entityManager->flush();
        }

        return $this->redirectToRoute('civility_index');
    }
}
