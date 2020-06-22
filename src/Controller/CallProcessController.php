<?php


namespace App\Controller;

use App\Entity\Call;
use App\Entity\CallProcessing;
use App\Form\CallProcessingType;
use App\Form\CallType;
use App\Repository\CallRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/call/process")
 */
class CallProcessController extends AbstractController
{
    /**
     * @Route("/{callId}", name="call_process", methods={"GET"})
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
}
