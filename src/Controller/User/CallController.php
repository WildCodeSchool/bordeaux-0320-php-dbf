<?php

namespace App\Controller\User;

use App\Entity\Call;
use App\Service\StepForCallDataMaker;
use DateInterval;
use DateTime;
use App\Entity\RecallPeriod;
use App\Entity\User;
use App\Form\CallType;
use App\Form\RecipientType;
use App\Repository\CallRepository;
use App\Repository\ClientRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CallOnTheWayDataMaker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/call")
 */
class CallController extends AbstractController
{
    /**
     * @Route("/", name="call_index", methods={"GET"})
     * @param CallRepository $callRepository
     * @return Response
     * @throws \Exception
     */
    public function index(CallRepository $callRepository): Response
    {

        return $this->render('call/index.html.twig', [
            'calls' => $callRepository->findCallsAddedToday(2)
        ]);
    }

    /**
     * @Route("/add", name="call_add", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CallRepository $callRepository
     * @param StepForCallDataMaker $stepForCallDataMaker
     * @return Response
     */
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        CallRepository $callRepository,
        StepForCallDataMaker $stepForCallDataMaker
    ): Response {
        //cette ligne sera aussi à remplacer par app->getUser();
        $addedCalls = $callRepository->findCallsAddedToday(2);
        $steps = [];
        foreach ($addedCalls as $addedCall) {
            $steps[] = $stepForCallDataMaker->stepMaker($addedCall);
        }
        dd($steps);
        $call          = new Call();
        $form          = $this->createForm(CallType::class, $call);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Cette ligne sera à remplacer par app->getUser();
            $author = $entityManager->getRepository(User::class)->findOneById(2);

            $call->setAuthor($author);
            $call->setIsUrgent(false);
            if ($call->getRecallPeriod()->getIdentifier() === RecallPeriod::URGENT) {
                $call->setIsUrgent(true);
            }

            $client = $call->getClient();
            $vehicle = $call->getVehicle();
            $vehicle->setClient($client);

            $entityManager->persist($call);
            $entityManager->flush();

            return $this->redirectToRoute('call_add');
        }
        return $this->render('call/add.html.twig', [
            'call'          => $call,
            'form'          => $form->createView(),
            'calls'         => $addedCalls
        ]);
    }

    /**
     * @Route("/{id}", name="call_show", methods={"GET"})
     * @param Call $call
     * @return Response
     */
    public function show(Call $call): Response
    {
        return $this->render('call/show.html.twig', [
            'call' => $call,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="call_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Call $call
     * @return Response
     */
    public function edit(Request $request, Call $call): Response
    {
        $form = $this->createForm(CallType::class, $call);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('call_index');
        }

        return $this->render('call/edit.html.twig', [
            'call' => $call,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="call_delete", methods={"DELETE"})
     * @param Request $request
     * @param Call $call
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Call $call, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$call->getId(), $request->request->get('_token'))) {
            $entityManager->remove($call);
            $entityManager->flush();
        }
        return $this->redirectToRoute('call_index');
    }

    /**
     * @Route("/recipient/form", name="recipient_form", methods={"POST"})
     */
    public function recipientForm(): Response
    {
        $call          = new Call();
        $recipientForm = $this->createForm(RecipientType::class, $call);

        return $this->render('call/_form_recipients.html.twig', [
            'recipientForm' => $recipientForm->createView(),
        ]);
    }

    /**
     * @Route("/search/{phoneNumber}", name="search_calls_for_numbers", methods={"GET"})
     * @param ClientRepository $clientRepository
     * @param CallRepository $callRepository
     * @param CallOnTheWayDataMaker $callOnTheWayDataMaker
     * @return JsonResponse
     */
    public function listAllCallsOnTheWayByPhoneNumber(
        ClientRepository $clientRepository,
        CallRepository $callRepository,
        CallOnTheWayDataMaker $callOnTheWayDataMaker,
        $phoneNumber
    ): JsonResponse {
        $client = $clientRepository->findOneByPhone($phoneNumber);

        $data = ['client' => [
            'client_id' => null]
        ];

        if ($client) {
            $data   = $callOnTheWayDataMaker->arrayMaker(
                $client,
                $callRepository->callsOnTheWayForClient($client->getId())
            );
        }
        return new JsonResponse([
            $data
        ]);
    }
}
