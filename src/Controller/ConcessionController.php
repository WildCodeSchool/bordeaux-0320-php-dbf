<?php

namespace App\Controller;

use App\Entity\Concession;
use App\Form\ConcessionType;
use App\Repository\ConcessionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/concession")
 * @IsGranted("ROLE_ADMIN")
 */
class ConcessionController extends AbstractController
{
    /**
     * @Route("/", name="concession_index", methods={"GET"})
     */
    public function index(ConcessionRepository $concessionRepository): Response
    {
        return $this->render('concession/index.html.twig', [
            'concessions' => $concessionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="concession_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $concession = new Concession();
        $formConcession= $this->createForm(ConcessionType::class, $concession);
        $formConcession->handleRequest($request);

        if ($formConcession->isSubmitted() && $formConcession->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($concession);
            $entityManager->flush();
            $this->addFlash("success", "Vous avez bien ajouté une concession");
        } else {
            $errors['name'] = $formConcession['name']->getErrors();
            $errors['address'] = $formConcession['address']->getErrors();
            $errors['postcode'] = $formConcession['postcode']->getErrors();
            $errors['city'] = $formConcession['city']->getErrors();
            $errors['brand'] = $formConcession['brand']->getErrors();
            $errors['phone'] = $formConcession['phone']->getErrors();
            $errors['town'] = $formConcession['town']->getErrors();
            foreach ($errors as $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $this->addFlash("error", $error->getMessage());
                }
            }
        }
        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/{id}", name="concession_show", methods={"GET"})
     */
    public function show(Concession $concession): Response
    {
        return $this->render('concession/show.html.twig', [
            'concession' => $concession,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="concession_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Concession $concession): Response
    {
        $form = $this->createForm(ConcessionType::class, $concession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('concession/edit.html.twig', [
            'concession' => $concession,
            'form_concession' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_concession", methods={"DELETE"})
     * @return JsonResponse
     */
    public function delete(Request $request, Concession $concession): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($concession);
        $entityManager->flush();

        $response = new JsonResponse();
        $status = JsonResponse::HTTP_NO_CONTENT;
        $response->setStatusCode($status);

        return $response;
    }
}
