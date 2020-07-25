<?php

namespace App\Controller\Admin;

use App\Entity\CityHead;
use App\Entity\Concession;
use App\Entity\ConcessionHead;
use App\Entity\ServiceHead;
use App\Form\CityHeadType;
use App\Repository\CityHeadRepository;
use App\Repository\CityRepository;
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
 * @Route("/city/head")
 * @IsGranted("ROLE_ADMIN")
 */
class CityHeadController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="city_head_edit", methods={"GET","POST"})
     * @param Request $request
     * @param CityHead $cityHead
     * @param EntityManagerInterface $entityManager
     * @param CityRepository $cityRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function edit(
        Request $request,
        CityHead $cityHead,
        EntityManagerInterface $entityManager,
        CityRepository $cityRepository,
        UserRepository $userRepository
    ): Response {
        $form = $this->createForm(CityHeadType::class, $cityHead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['id'=> $request->request->get('city_head')['user']]);


            //new services and concessions
            $city = $cityRepository->findOneBy(['id'=> $request->request->get('city_head')['city']]);
            $concessions = $city->getConcessions();

            for ($i = 0; $i< count($concessions); $i++) {
                $concessionHead = new ConcessionHead();
                $concessionHead->setUser($user);
                $concessionHead->setConcession($concessions[$i]);
                $entityManager->persist($concessionHead);

                $services = $concessions[$i]->getServices();
                for ($j=0; $j<count($services); $j++) {
                    $serviceHead = new ServiceHead();
                    $serviceHead->setUser($user);
                    $serviceHead->setService($services[$j]);
                    $entityManager->persist($serviceHead);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('service_head_index');
        }

        return $this->render('city_head/edit.html.twig', [
            'city_head' => $cityHead,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="city_head_delete", methods={"DELETE"})
     * @param $id
     * @param Request $request
     * @param CityHead $cityHead
     * @param CityHeadRepository $cityHeadRepository
     * @param ConcessionRepository $concessionRepository
     * @param ConcessionHeadRepository $concessionHeadRepository
     * @param ServiceHeadRepository $serviceHeadRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(
        $id,
        Request $request,
        CityHeadRepository $cityHeadRepository,
        ConcessionHeadRepository $concessionHeadRepository,
        ServiceHeadRepository $serviceHeadRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $cityHead = $cityHeadRepository->findOneById((int)$id);
        $user = $cityHead->getUser();

        $concessionHeadsInHeadCity = $concessionHeadRepository->getAllConcessionHeadsInCity($user, (int)$id);


        if ($this->isCsrfTokenValid('delete'.$cityHead->getId(), $request->request->get('_token'))) {
            foreach ($concessionHeadsInHeadCity as $head) {
                if ($head->getUser() === $user) {
                    $serviceHeadsInConcession = $serviceHeadRepository
                        ->getAllServiceHeadsInConcession($user, $head->getId());
                    foreach ($serviceHeadsInConcession as $serviceHead) {
                        if ($serviceHead->getUser() === $user) {
                            $entityManager->remove($serviceHead);
                        }
                    }
                    $entityManager->remove($head);
                }
            }
            $entityManager->remove($cityHead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_head_index');
    }
}
