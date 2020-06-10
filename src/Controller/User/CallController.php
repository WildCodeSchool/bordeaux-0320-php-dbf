<?php

namespace App\Controller\User;

use App\Entity\Call;
use App\Form\CallType;
use App\Form\RecipientType;
use App\Repository\CallRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManager;
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
     */
    public function index(CallRepository $callRepository): Response
    {
        return $this->render('call/index.html.twig', [
            'calls' => $callRepository->findAll(),
        ]);
    }

    /**
     * @Route("/add", name="call_add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request): Response
    {
        $call          = new Call();
        $form          = $this->createForm(CallType::class, $call);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            //add isUrgent
            $data = $form->getData();
            dd($data);
            $entityManager->persist($call);

            $entityManager->flush();

            return $this->redirectToRoute('call_index');
        }

        return $this->render('call/add.html.twig', [
            'call'          => $call,
            'form'          => $form->createView(),
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
     * @return Response
     */
    public function delete(Request $request, Call $call): Response
    {
        if ($this->isCsrfTokenValid('delete'.$call->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
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
     * @return JsonResponse
     */
    public function listAllCallsOnTheWayByPhoneNumber(
        ClientRepository $clientRepository,
        CallRepository $callRepository,
        $phoneNumber
    ): JsonResponse {
        $client = $clientRepository->findOneByPhone($phoneNumber);
        //$calls = $callRepository->callsOnTheWayForClient($client->getId());
        if ($client) {
            return new JsonResponse([
                'client_id' => $client->getId(),
                'client_name' => $client->getName(),
                'client_phone' => $client->getPhone()
            ]);
        } else {
            return new JsonResponse([
                'client_id' => null,
            ]);
        }
    }
}
