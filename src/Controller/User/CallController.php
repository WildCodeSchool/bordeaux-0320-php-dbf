<?php

namespace App\Controller\User;

use App\Entity\Call;
use App\Service\CallTreatmentDataMaker;
use DateInterval;
use DateTime;
use App\Entity\RecallPeriod;
use App\Entity\User;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/call")
 * @IsGranted("ROLE_COLLABORATOR")
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
     * @param CallTreatmentDataMaker $callTreatmentDataMaker
     * @return Response
     */
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        VehicleRepository $vehicleRepository,
        ClientRepository $clientRepository,      
        CallRepository $callRepository,
        CallTreatmentDataMaker $callTreatmentDataMaker
    ): Response {
        //cette ligne sera à remplacer par app->getUser();
        $addedCalls = $callRepository->findCallsAddedToday(2);

        $steps = [];
        foreach ($addedCalls as $addedCall) {
            $steps[ $addedCall->getId()] = $callTreatmentDataMaker->stepMaker($addedCall);
            $steps[ $addedCall->getId()]['lastStepName'] = $callTreatmentDataMaker->getLastTreatment($addedCall);
        }

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
            if ($request->request->get('call')['client_id'] != '') {
                $client = $clientRepository->findOneById($request->request->get('call')['client_id']);
                $call->setClient($client);
                $entityManager->persist($client);
            }

            $vehicle = $call->getVehicle();
            if ($request->request->get('call')['vehicle_id'] != '') {
                $vehicle = $vehicleRepository->findOneById($request->request->get('call')['vehicle_id']);
                $call->setVehicle($vehicle);
                $entityManager->persist($vehicle);
            }

            $vehicle->setClient($client);

            $entityManager->persist($call);
            $entityManager->flush();
            $this->addFlash('success', 'Appel ajouté ');

            return $this->redirectToRoute('call_add');
        }
        return $this->render('call/add.html.twig', [
            'call'          => $call,
            'form'          => $form->createView(),
            'addedCalls'         => $addedCalls,
            'steps'         => $steps
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
