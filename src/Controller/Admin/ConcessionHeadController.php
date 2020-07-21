<?php

namespace App\Controller\Admin;

use App\Entity\ConcessionHead;
use App\Entity\ServiceHead;
use App\Form\ConcessionHeadType;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param $id
     * @param Request $request
     * @param ConcessionHead $concessionHead
     * @param UserRepository $userRepository
     * @param ConcessionRepository $concessionRepository
     * @param ServiceHeadRepository $serviceHeadRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(
        $id,
        Request $request,
        ConcessionHead $concessionHead,
        UserRepository $userRepository,
        ConcessionRepository $concessionRepository,
        ConcessionHeadRepository $concessionHeadRepository,
        ServiceHeadRepository $serviceHeadRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $form = $this->createForm(ConcessionHeadType::class, $concessionHead);
        $form->handleRequest($request);

        //concessionA
        $concessionRegistered = $concessionHeadRepository->findOneBy(['id'=>$id]);
        $concession = $concessionRegistered->getConcession();
        //Serv A1 A2 A3
        $servicesRegistered = $concession->getServices();
        dd($servicesRegistered);


        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['id'=> $request->request->get('concession_head')['user']]);
            /**1suppr A1A2A
            **/



            //new services
            $concession = $concessionRepository
                ->findBy(['id'=> $request->request->get('concession_head')['concession']]);
            $services = $concession[0]->getServices();
            for ($i=0; $i<count($services); $i++) {
                $serviceHead = new ServiceHead();
                $serviceHead->setUser($user);
                $serviceHead->setService($services[$i]);
                $entityManager->persist($serviceHead);
            }
            $entityManager->flush();

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
