<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index()
    {
        return $this->render('edit.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
    /**
     * @Route("edit/profile/{id}", name="profile_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function editProfile(Request $request, User $user): Response
    {
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($form->get('password')->getData());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
