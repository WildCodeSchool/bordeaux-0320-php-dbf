<?php

namespace App\Controller\Admin;

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
     * @param CivilityRepository $civilityRepository
     * @return Response
     */
    public function index(CivilityRepository $civilityRepository): Response
    {
        return $this->render('civility/index.html.twig', [
            'civilities' => $civilityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="civility_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
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
            $this->addFlash("success", "Vous avez bien ajouté une civilité");
        } else {
            $errors = $formCivility['name']->getErrors();
            foreach ($errors as $error) {
                $this->addFlash("error", $error->getMessage());
            }
        }
        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/{id}", name="civility_show", methods={"GET"})
     * @param Civility $civility
     * @return Response
     */
    public function show(Civility $civility): Response
    {
        return $this->render('civility/show.html.twig', [
            'civility' => $civility,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="civility_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Civility $civility
     * @return Response
     */
    public function edit(Request $request, Civility $civility): Response
    {
        $form = $this->createForm(CivilityType::class, $civility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('civility/edit.html.twig', [
            'civility' => $civility,
            'form_civility' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_civility", methods={"DELETE"})
     * @return JsonResponse
     */
    public function delete(Request $request, Civility $civility): JsonResponse
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($civility);
        $entityManager->flush();

        $response = new JsonResponse();
        $status = JsonResponse::HTTP_NO_CONTENT;
        $response->setStatusCode($status);

        return $response;
    }
}
