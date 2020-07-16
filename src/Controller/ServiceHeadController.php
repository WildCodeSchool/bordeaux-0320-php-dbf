<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\CityHead;
use App\Entity\ConcessionHead;
use App\Entity\ServiceHead;
use App\Form\CityHeadType;
use App\Form\ConcessionHeadType;
use App\Form\ServiceHeadType;
use App\Repository\CityHeadRepository;
use App\Repository\CityRepository;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\UserRepository;
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
     * @param ConcessionHeadRepository $concessionHeadRepository
     * @param CityHeadRepository $cityHeadRepository
     * @return Response
     */
    public function index(
        ServiceHeadRepository $serviceHeadRepository,
        ConcessionHeadRepository $concessionHeadRepository,
        CityHeadRepository $cityHeadRepository
    ): Response {
        return $this->render('service_head/index.html.twig', [
            'service_heads' => $serviceHeadRepository->findAll(),
            'concession_heads'=> $concessionHeadRepository->findAll(),
            'city_heads'=> $cityHeadRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="service_head_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CityRepository $cityRepository
     * @param ConcessionRepository $concessionRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CityRepository $cityRepository,
        ConcessionRepository $concessionRepository,
        UserRepository $userRepository
    ): Response {
        $serviceHead = new ServiceHead();
        $form = $this->createForm(ServiceHeadType::class, $serviceHead);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($serviceHead);
            $entityManager->flush();
            return $this->redirectToRoute('service_head_index');
        }

        $concessionHead = new ConcessionHead();
        $formConcessionHead = $this->createForm(ConcessionHeadType::class, $concessionHead);
        $formConcessionHead->handleRequest($request);
        if ($formConcessionHead->isSubmitted() && $formConcessionHead->isValid()) {
            $user = $userRepository->findOneById($request->request->get('concession_head')['user']);
            $concession = $concessionRepository->findOneById($request->request->get('concession_head')['concession']);
            $services = $concession->getServices();
            for ($i=0; $i<count($services); $i++) {
                $serviceHead = new ServiceHead();
                $serviceHead->setUser($user);
                $serviceHead->setService($services[$i]);
                $entityManager->persist($serviceHead);
            }
            $entityManager->persist($concessionHead);
            $entityManager->flush();

            return $this->redirectToRoute('service_head_index');
        }

        $cityHead = new CityHead();
        $formCityHead = $this->createForm(CityHeadType::class, $cityHead);
        $formCityHead->handleRequest($request);
        if ($formCityHead->isSubmitted() &&  $formCityHead->isValid()) {
            $city = $cityRepository->findOneById($request->request->get('city_head')['city']);
            $user = $userRepository->findOneById($request->request->get('city_head')['user']);
            $concessions = $city->getConcessions();
            for ($i = 0; $i< count($concessions); $i++) {
                $concessionHead = new ConcessionHead();
                $concessionHead->setUser($user);
                $concessionHead->setConcession($concessions[$i]);
                $entityManager->persist($concessionHead);
            }
            $entityManager->persist($cityHead);
            $entityManager->flush();

            return $this->redirectToRoute('service_head_index');
        }


        return $this->render('service_head/new.html.twig', [
            'service_head' => $serviceHead,
            'form' => $form->createView(),
            'concession_head' => $concessionHead,
            'form_head_concession'=> $formConcessionHead->createView(),
            'city_head'=> $cityHead,
            'form_head_city'=> $formCityHead->createView()
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
