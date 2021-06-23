<?php

namespace App\Controller;

use App\Entity\Call;
use App\Entity\User;
use App\Repository\CallRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CallRecuperatorController extends AbstractController
{
    /**
     * @Route("/call/recuperator/{recipient}", name="call_recuperator")
     * @param User $recipient
     * @param CallRepository $callRepository
     * @return Response
     */
    public function index(User $recipient, CallRepository $callRepository, Security $security): Response
    {
        if ($recipient !== $security->getUser()) {
            return $this->redirectToRoute( 'user_home');
        }
        $calls = $callRepository->everyCallsByUser($recipient);
        return $this->render('call_recuperator/index.html.twig', [
            'calls' => $calls,
        ]);
    }

    /**
     * @Route("/call/reprocess/{call}", name="call_reprocess")
     * @param User $recipient
     * @param CallRepository $callRepository
     * @return Response
     */
    public function reprocess(Call $call, EntityManagerInterface $manager): Response
    {
        if ($call->getRecipient() !== $this->getUser()) {
            return $this->redirectToRoute( 'user_home');
        }
        $call->setIsProcessEndedToNull();
        $manager->persist($call);
        $manager->flush();
        return $this->redirectToRoute('user_home');
    }
}
