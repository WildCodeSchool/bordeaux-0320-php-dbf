<?php

namespace App\Controller\Admin;

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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/head")
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceHeadController extends AbstractController
{
    const SERVICE_HEAD_INDEX = 'service_head_index';
    const CONCESSION_HEAD    = 'concession_head';
    const CITY_HEAD          = 'city_head';
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
            $this->addFlash('success', 'Responsabilité ajoutée');

            return $this->redirectToRoute(self::SERVICE_HEAD_INDEX);
        }

        $concessionHead = new ConcessionHead();
        $formConcessionHead = $this->createForm(ConcessionHeadType::class, $concessionHead);
        $formConcessionHead->handleRequest($request);
        if ($formConcessionHead->isSubmitted() && $formConcessionHead->isValid()) {
            $user = $userRepository->findOneById($request->request->get(self::CONCESSION_HEAD)['user']);
            $concession = $concessionRepository
                ->findOneById($request->request->get(self::CONCESSION_HEAD)['concession']);
            $services = $concession->getServices();
            for ($i=0; $i<count($services); $i++) {
                $serviceHead = new ServiceHead();
                $serviceHead->setUser($user);
                $serviceHead->setService($services[$i]);
                $entityManager->persist($serviceHead);
            }
            $entityManager->persist($concessionHead);
            $entityManager->flush();
            $this->addFlash('success', 'Responsabilité ajoutée');
            return $this->redirectToRoute(self::SERVICE_HEAD_INDEX);
        }

        $cityHead = new CityHead();
        $formCityHead = $this->createForm(CityHeadType::class, $cityHead);
        $formCityHead->handleRequest($request);
        if ($formCityHead->isSubmitted() &&  $formCityHead->isValid()) {
            $city = $cityRepository->findOneById($request->request->get(self::CITY_HEAD)['city']);
            $user = $userRepository->findOneById($request->request->get(self::CITY_HEAD)['user']);
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
            $entityManager->persist($cityHead);
            $entityManager->flush();
            $this->addFlash('success', 'Responsabilité ajoutée');

            return $this->redirectToRoute(self::SERVICE_HEAD_INDEX);
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
            $this->addFlash('success', 'Responsable de Service supprimé');
        }

        return $this->redirectToRoute(self::SERVICE_HEAD_INDEX);
    }
}
