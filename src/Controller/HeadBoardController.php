<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\HeadBoardData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HeadBoardController
 * @package App\Controller
 */
class HeadBoardController extends AbstractController
{
    /**
     * @Route("/head/board", name="head_board")
     */
    public function index(UserRepository $userRepository, HeadBoardData $headBoardData)
    {
        $user = $this->getUser();
        $headServices = $user->getServiceHeads();
        $dataForServices = $headBoardData->makeDataForHeads($headServices);

        return $this->render('head_board/index.html.twig', [
            'services' => $dataForServices
        ]);
    }
}
