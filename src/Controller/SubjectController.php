<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subject")
 * @IsGranted("ROLE_ADMIN")
 */
class SubjectController extends AbstractController
{
    /**
     * @Route("/", name="subject_index", methods={"GET"})
     */
    public function index(SubjectRepository $subjectRepository): Response
    {
        return $this->render('subject/index.html.twig', [
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="subject_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $subject = new Subject();
        $formSubject = $this->createForm(SubjectType::class, $subject);
        $formSubject->handleRequest($request);

        if ($formSubject->isSubmitted() && $formSubject->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subject);
            $entityManager->flush();
            $this->addFlash("success", "Vous avez bien ajouté un motif d'appel");
        } else {
            $errors['name'] = $formSubject['name']->getErrors();
            $errors['city'] = $formSubject['city']->getErrors();
            $errors['isForAppWorkshop'] = $formSubject['isForAppWorkshop']->getErrors();
            foreach ($errors as $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $this->addFlash("error", $error->getMessage());
                }
            }
        }
        return $this->redirectToRoute('admin_dashboard');
        return $this->render('subject/new.html.twig', [
            'subject' => $subject,
            'form_subject' => $formSubject->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="subject_show", methods={"GET"})
     */
    public function show(Subject $subject): Response
    {
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="subject_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Subject $subject): Response
    {
        $formSubject = $this->createForm(SubjectType::class, $subject);
        $formSubject->handleRequest($request);

        if ($formSubject->isSubmitted() && $formSubject->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('subject/edit.html.twig', [
            'subject' => $subject,
            'form_subject' => $formSubject->createView(),
        ]);
    }


    /**
     * @return JsonResponse
     * @Route("/delete/{id}", name="delete_subject", methods={"DELETE"})
     */

    public function delete(Subject $subject, Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($subject);
        $entityManager->flush();

        $response = new JsonResponse();
        $status = JsonResponse::HTTP_OK;
        $response->setStatusCode($status);

        return $response;
    }
}
