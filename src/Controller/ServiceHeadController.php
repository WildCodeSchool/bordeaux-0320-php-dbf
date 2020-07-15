<?php

namespace App\Controller;

use App\Entity\ServiceHead;
use App\Form\ServiceHeadType;
use App\Repository\ServiceHeadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/head")
 */
class ServiceHeadController extends AbstractController
{
    /**
     * @Route("/", name="service_head_index", methods={"GET"})
     * @param ServiceHeadRepository $serviceHeadRepository
     * @return Response
     */
    public function index(ServiceHeadRepository $serviceHeadRepository): Response
    {
        return $this->render('service_head/index.html.twig', [
            'service_heads' => $serviceHeadRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="service_head_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serviceHead = new ServiceHead();
        $form = $this->createForm(ServiceHeadType::class, $serviceHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($serviceHead);
            $entityManager->flush();

            return $this->redirectToRoute('service_head_index');
        }

        return $this->render('service_head/new.html.twig', [
            'service_head' => $serviceHead,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="service_head_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ServiceHead $serviceHead, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceHeadType::class, $serviceHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('service_head_index');
        }

        return $this->render('service_head/edit.html.twig', [
            'service_head' => $serviceHead,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_head_delete", methods={"DELETE"})
     * @param Request $request
     * @param ServiceHead $serviceHead
     * @return Response
     */
    public function delete(Request $request, ServiceHead $serviceHead): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceHead->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($serviceHead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_head_index');
    }
}
