<?php

namespace App\Controller\Admin;

use App\Entity\ConcessionHead;
use App\Form\ConcessionHeadType;
use App\Repository\ConcessionHeadRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/concession/head")
 * @IsGranted("ROLE_ADMIN")
 */
class ConcessionHeadController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="concession_head_edit", methods={"GET","POST"})
     * @param Request $request
     * @param ConcessionHead $concessionHead
     * @return Response
     */
    public function edit(Request $request, ConcessionHead $concessionHead): Response
    {
        $form = $this->createForm(ConcessionHeadType::class, $concessionHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('service_head_index');
        }

        return $this->render('concession_head/edit.html.twig', [
            'concession_head' => $concessionHead,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="concession_head_delete", methods={"DELETE"})
     * @param Request $request
     * @param ConcessionHead $concessionHead
     * @return Response
     */
    public function delete(Request $request, ConcessionHead $concessionHead): Response
    {
        if ($this->isCsrfTokenValid('delete'.$concessionHead->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($concessionHead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_head_index');
    }
}
