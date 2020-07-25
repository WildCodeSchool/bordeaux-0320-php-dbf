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
        $headsId= [];
        foreach ($servicesRegistered as $service) {
            $headsId [] = $service->getId();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['id'=> $request->request->get('concession_head')['user']]);
            /**1suppsr A1A2A
            **/


            $userServiceHeads = $serviceHeadRepository->findByUser($user);
            for ($i= 0; $i< count($userServiceHeads); $i++) {
                if (in_array($userServiceHeads[$i]->getService()->getId(), $headsId)) {
                    $entityManager->remove($userServiceHeads[$i]);
                }
            }

            // 2 new services
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
     * @param $id
     * @param Request $request
     * @param ConcessionHead $concessionHead
     * @param EntityManagerInterface $entityManager
     * @param ConcessionHeadRepository $concessionHeadRepository
     * @param ServiceHeadRepository $serviceHeadRepository
     * @return Response
     */
    public function delete(
        $id,
        Request $request,
        ConcessionHead $concessionHead,
        EntityManagerInterface $entityManager,
        ConcessionHeadRepository $concessionHeadRepository,
        ServiceHeadRepository $serviceHeadRepository
    ): Response {
        $concessionHead = $concessionHeadRepository->findOneById((int)$id);
        $user = $concessionHead->getUser();

        $serviceHeadsInConcession = $serviceHeadRepository->getAllServiceHeadsInConcession($user, (int)$id);


        if ($this->isCsrfTokenValid('delete'.$concessionHead->getId(), $request->request->get('_token'))) {
            $entityManager->remove($concessionHead);
            foreach ($serviceHeadsInConcession as $serviceHead) {
                if ($serviceHead->getUser() === $user) {
                    $entityManager->remove($serviceHead);
                }
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_head_index');
    }
}
