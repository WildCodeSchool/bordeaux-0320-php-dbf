<?php

namespace App\Controller\User;

use App\Entity\Call;
use App\Entity\User;
use App\Events;
use App\Repository\SubjectRepository;
use App\Service\CallTreatmentDataMaker;
use App\Entity\RecallPeriod;
use App\Form\CallType;
use App\Form\RecipientType;
use App\Repository\CallRepository;
use App\Repository\ClientRepository;
use App\Repository\ServiceRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CallOnTheWayDataMaker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;


class SessionController extends AbstractController
{
    /**
     * @Route("/checksession", name="check_session", methods={"GET"})
     * @param CallRepository $callRepository
     * @return Response
     * @throws \Exception
     */
    public function index(CallRepository $callRepository): Response
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);

        if ($this->getUser()) {
            $response->setStatusCode(Response::HTTP_OK);
        }

        return $response;
    }

}
