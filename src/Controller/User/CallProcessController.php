<?php

namespace App\Controller\User;

use App\Entity\CallProcessing;
use App\Entity\CallTransfer;
use App\Entity\RecallPeriod;
use App\Events;
use App\Form\CallProcessingType;
use App\Repository\CallRepository;
use App\Repository\ContactTypeRepository;
use App\Repository\RecallPeriodRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Service\CallStepChecker;
use App\Service\CallTreatmentDataMaker;
use App\Twig\DateFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CallTransferType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/call/process")
 * @IsGranted("ROLE_COLLABORATOR")
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
     * @param ContactTypeRepository $contactTypeRepository
     * @param CallStepChecker $callStepChecker
     * @return JsonResponse
     */
    public function addCallProcess(
        $callId,
        CallRepository $callRepository,
        ContactTypeRepository $contactTypeRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        CallStepChecker $callStepChecker,
        RecallPeriodRepository $recallPeriodRepository
    ) {

        $contactType = $contactTypeRepository->findOneById(
            (int)$request->request->get('call_processing')['contactType']
        );
        $call = $callRepository->findOneById($callId);
        $call
            ->setIsProcessed(true)
            ->setAppointmentDate($callStepChecker->checkAppointmentDate($request))
            ->setIsAppointmentTaken($callStepChecker->checkAppointment($request))
            ->setIsUrgent(false)
            ->setRecallPeriod($recallPeriodRepository->findOneBy(['identifier' => RecallPeriod::AUTOUR_DE]))
            ->setClientCallback(0);

        if ($callStepChecker->isCallToBeEnded($request)) {
            $call->setIsProcessEnded(true);
            $call->setClientCallback(0);
        }
        $entityManager->persist($call);

        $callProcess = new CallProcessing();
        $form        = $this->createForm(CallProcessingType::class, $callProcess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $callProcess->setContactType($contactType);
            $callProcess->setReferedCall($call);
            $entityManager->persist($callProcess);
            $entityManager->flush();
        }
        return new JsonResponse([
            'callId' => $callId,
            'date'   => DateFormatter::formatDate($callProcess->getCreatedAt()),
            'time'  => DateFormatter::formatTime($callProcess->getCreatedAt()),
            'colors' => CallTreatmentDataMaker::stepMakerForProcess($callProcess),
            'is_ended' => $call->getIsProcessEnded(),
        ]);
    }

    /**
     * @Route("/{callId}/transfer/{cityId}/{concessionId}/{serviceId}", name="call_transfer", methods={"GET"})
     * @param int $callId
     * @param CallRepository $callRepository
     * @param int $cityId
     * @param int $concessionId
     * @param int $serviceId
     * @return Response
     */
    public function callTransfer(
        $callId,
        CallRepository $callRepository,
        $cityId = 0,
        $concessionId = 0,
        $serviceId = 0
    ) {
        $call = $callRepository->findOneById($callId);
        $call->setCityTransfer($call->getRecipient()->getService()->getConcession()->getTown()->getId());
        $call->setConcessionTransfer($call->getRecipient()->getService()->getConcession()->getId());
        $call->setServiceTransfer($call->getRecipient()->getService()->getId());
        if ($cityId != 0) {
            $call->setCityTransfer($cityId);
        }
        if ($concessionId != 0) {
            $call->setConcessionTransfer($concessionId);
        }
        if ($serviceId != 0) {
            $call->setServiceTransfer($serviceId);
        }


        $form = $this->createForm(CallTransferType::class, $call);

        return $this->render('call_process/call_transfer.html.twig', [
            'call'          => $call,
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/{callId}/dotransfer", name="call_transfer_do", methods={"POST"})
     * @param int $callId
     * @param CallRepository $callRepository
     * @return JsonResponse
     */
    public function doCallTransfer(
        $callId,
        CallRepository $callRepository,
        UserRepository $userRepository,
        ServiceRepository $serviceRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $call            = $callRepository->findOneById($callId);
        $fromWhom        = $call->getRecipient();
        $call->setRecipient($userRepository->findOneById($request->request->get('call_transfer')['recipient']));
        $toWhom          = $call->getRecipient();
        $byWhom          = $this->getUser();
        $transferComment = $request->request->get('call_transfer')['commentTransfer'];
        $transfer        = new CallTransfer();
        $transfer
            ->setReferedCall($call)
            ->setFromWhom($fromWhom)
            ->setByWhom($byWhom)
            ->setToWhom($toWhom)
            ->setCommentTransfer($transferComment);

        $entityManager->persist($transfer);
        $entityManager->persist($call);
        $entityManager->flush();



        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->setData([
            'callId' => $call->getId(),
        ]);
        return $response;
    }

    /**
     * @Route("/{callId}/transferto/{userId}", name="call_transfer_to", methods={"GET"})
     * @param int $callId
     * @param int $userId
     * @param CallRepository $callRepository
     * @return JsonResponse
     */
    public function callTransferTo(
        $callId,
        $userId,
        CallRepository $callRepository,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $call            = $callRepository->findOneById($callId);
        $fromWhom        = $call->getRecipient();
        $call->setRecipient($userRepository->findOneById($userId));
        $toWhom          = $call->getRecipient();
        $byWhom          = $this->getUser();
        $transfer        = new CallTransfer();
        $transfer
            ->setReferedCall($call)
            ->setFromWhom($fromWhom)
            ->setByWhom($byWhom)
            ->setToWhom($toWhom);

        $entityManager->persist($transfer);
        $entityManager->persist($call);
        $entityManager->flush();

        $event = new GenericEvent($call);
        $eventDispatcher->dispatch($event, Events::CALL_INCOMING);

        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_ACCEPTED);
        return $response;
    }

    /**
     * @Route("/{callId}/callback", name="client_callback", methods={"GET"})
     * @param int $callId
     * @param CallRepository $callRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function clientCallback(
        $callId,
        CallRepository $callRepository,
        EntityManagerInterface $entityManager
    ) {
        $call = $callRepository->findOneById($callId);
        $countCallback = $call->getClientCallback();
        $countCallback++;
        $call->setClientCallback($countCallback);
        $entityManager->persist($call);
        $entityManager->flush();
        $this->addFlash('success', 'Rappel par le client enregistré');

        return $this->redirectToRoute('call_add');
    }
}
