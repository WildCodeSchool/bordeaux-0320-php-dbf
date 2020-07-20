<?php

namespace App\Controller;

use App\Entity\CityHead;
use App\Entity\ConcessionHead;
use App\Entity\ServiceHead;
use App\Form\CityHeadType;
use App\Repository\CityHeadRepository;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/city/head")
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
     */
    public function delete(Request $request, CityHead $cityHead): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cityHead->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cityHead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_head_index');
    }
}
