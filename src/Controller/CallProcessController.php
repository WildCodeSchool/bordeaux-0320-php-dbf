<?php


namespace App\Controller;

use App\Entity\CallProcessing;
use App\Form\CallProcessingType;
use App\Repository\CallRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CallTransferType;

/**
 * @Route("/call/process")
 */
class CallProcessController extends AbstractController
{
    /**
     * @Route("/{callId}", name="call_process", methods={"GET"})
     * @param int $callId
     * @param CallRepository $callRepository
     * @return Response
     */
    public function callProcessing($callId, CallRepository $callRepository)
    {
        $call = $callRepository->findOneById($callId);
        $callProcess          = new CallProcessing();
        $form                 = $this->createForm(CallProcessingType::class, $callProcess);

        return $this->render('call_process/call_process.html.twig', [
            'call'          => $call,
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/{callId}/add", name="add_call_process", methods={"POST"})
     * @param int $callId
     * @param CallRepository $callRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function addCallProcess(
        $callId,
        CallRepository $callRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $call = $callRepository->findOneById($callId);

        if (is_null($call->getIsProcessed())) {
            $call->setIsProcessed(true);
            $entityManager->persist($call);
        }

        $callProcess = new CallProcessing();
        $form        = $this->createForm(CallProcessingType::class, $callProcess);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $callProcess->setReferedCall($call);
            $entityManager->persist($callProcess);
            $entityManager->flush();
            return $this->redirectToRoute('user_home');
        }
    }

    /**
     * @Route("/{callId}/transfer", name="call_transfer", methods={"GET"})
     * @param int $callId
     * @param CallRepository $callRepository
     * @return Response
     */
    public function callTransfer($callId, CallRepository $callRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $call = $callRepository->findOneById($callId);
        $form                 = $this->createForm(CallTransferType::class, $call);
        $form->handleRequest($request);
        return $this->render('call_process/call_transfer.html.twig', [
            'call'          => $call,
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/{callId}/dotransfer", name="call_transfer_do", methods={"POST"})
     * @param int $callId
     * @param CallRepository $callRepository
     * @return Response
     */
    public function doCallTransfer(
        $callId,
        CallRepository $callRepository,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager)
    {
        $call = $callRepository->findOneById($callId);
        $call->setRecipient($userRepository->findOneById($request->request->get('call_transfer')['recipient']));
        $form = $this->createForm(CallTransferType::class, $call);
        $form->handleRequest($request);
        dd($form);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($call);
            $entityManager->flush();
            return $this->redirectToRoute('cell_home');
        }

        return $this->render('call_process/call_transfer.html.twig', [
            'call'          => $call,
            'form'          => $form->createView(),
        ]);
    }
}
