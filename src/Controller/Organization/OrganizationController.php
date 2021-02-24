<?php

namespace App\Controller\Organization;


use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrganizationController extends AbstractController
{
    /**
     * @Route("/organization", name="organization")
     */

    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('organization/index.html.twig', [
           'cities' => $cityRepository->findBy([], ['name' => 'ASC'])
        ]);
    }
}
