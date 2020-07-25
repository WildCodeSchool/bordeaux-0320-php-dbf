<?php

namespace App\Controller\Admin;

use App\Entity\ConcessionHead;
use App\Entity\ServiceHead;
use App\Form\ConcessionHeadType;
use App\Repository\ConcessionHeadRepository;
use App\Repository\ConcessionRepository;
use App\Repository\ServiceHeadRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/concession/head")
 * @IsGranted("ROLE_ADMIN")
 */
class ConcessionHeadController extends AbstractController
{
    /**
     * @Route("/{id}", name="concession_head_delete", methods={"DELETE"})
     * @param $id
     * @param Request $request
     * @param ConcessionHead $concessionHead
     * @param EntityManagerInterface $entityManager
     * @param ConcessionHeadRepository $concessionHeadRepository
     * @param ServiceHeadRepository $serviceHeadRepository
     * @return Response
     */
    public function delete(
        $id,
        Request $request,
        ConcessionHead $concessionHead,
        EntityManagerInterface $entityManager,
        ConcessionHeadRepository $concessionHeadRepository,
        ServiceHeadRepository $serviceHeadRepository
    ): Response {
        $concessionHead = $concessionHeadRepository->findOneById((int)$id);
        $user = $concessionHead->getUser();

        $serviceHeadsInConcession = $serviceHeadRepository->getAllServiceHeadsInConcession($user, (int)$id);


        if ($this->isCsrfTokenValid('delete'.$concessionHead->getId(), $request->request->get('_token'))) {
            $entityManager->remove($concessionHead);
            foreach ($serviceHeadsInConcession as $serviceHead) {
                if ($serviceHead->getUser() === $user) {
                    $entityManager->remove($serviceHead);
                }
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_head_index');
    }
}
