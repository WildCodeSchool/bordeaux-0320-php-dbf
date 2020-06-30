<?php


namespace App\Controller;

use App\Entity\CallProcessing;
use App\Entity\CallTransfer;
use App\Form\CallProcessingType;
use App\Repository\CallProcessingRepository;
use App\Repository\CallRepository;
use App\Repository\CityRepository;
use App\Repository\ContactTypeRepository;
use App\Repository\UserRepository;
use App\Service\CallStepChecker;
use App\Service\CallTreatmentDataMaker;
use App\Twig\DateFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @return JsonResponse
     */
    public function addCallProcess(
        $callId,
        CallRepository $callRepository,
        ContactTypeRepository $contactTypeRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        CallStepChecker $callStepChecker
    ) {

        $contactType = $contactTypeRepository->findOneById(
            (int)$request->request->get('call_processing')['contactType']
        );
        $call = $callRepository->findOneById($callId);
        $call
            ->setIsProcessed(true)
            ->setAppointmentDate($callStepChecker->checkAppointmentDate($request))
            ->setIsAppointmentTaken($callStepChecker->checkAppointment($request));

        if ($callStepChecker->isCallToBeEnded($request)) {
            $call->setIsProcessEnded(true);
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
     * @param int $cityId
     * @param int $concessionId
     * @param int $serviceId
     * @param Request $request
     * @param CityRepository $cityRepository
     * @param CallRepository $callRepository
     * @return Response
     */
    public function callTransfer(
        $callId,
        CallRepository $callRepository,
        CityRepository $cityRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        $cityId = 0,
        $concessionId = 0,
        $serviceId = 0
    ) {
        $call = $callRepository->findOneById($callId);
        if ($cityId != 0) {
            $call->setCityTransfer($cityId);
        }
        if ($concessionId != 0) {
            $call->setConcessionTransfer($concessionId);
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
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $call            = $callRepository->findOneById($callId);
        $fromWhom        = $call->getRecipient();
        $call->setRecipient($userRepository->findOneById($request->request->get('call_transfer')['recipient']));
        $toWhom          = $call->getRecipient();
        $byWhom          = $this->getUser();
        $transferComment = $request->request->get('call_transfer')['comment'];
        $transfer        = new CallTransfer();
        $transfer
            ->setReferedCall($call)
            ->setFromWhom($fromWhom)
            ->setByWhom($byWhom)
            ->setToWhom($toWhom)
            ->setComment($transferComment);

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
}
