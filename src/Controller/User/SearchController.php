<?php

namespace App\Controller\User;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\CallRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     * @param SearchData $searchData
     * @param Request $request
     * @param CallRepository $callRepository
     * @return Response
     */
    public function index(SearchData $searchData, Request $request, CallRepository $callRepository)
    {
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        $searchedCalls = $callRepository->findSearch($searchData);

        return $this->render('search/index.html.twig', [
            'form'=> $form->createView(),
            'calls'=> $searchedCalls
        ]);
    }
}
