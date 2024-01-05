<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\AskForCodeType;
use App\Form\ResetPasswordType;
use App\Form\ValidateCodeType;
use App\Repository\UserRepository;
use App\Service\CodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use http\Message\Body;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    public function __construct(UserRepository $userRepository, SessionInterface $session, UserPasswordEncoderInterface  $encoder)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->encode = $encoder;
    }

    /**
     * @Route("/", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('user_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the 
        logout key on your firewall.');
    }

    /**
     * @Route("/reset", name="app_reset")
     */
    public function reset(Request $request, EntityManagerInterface $manager, MailerInterface $mailer)
    {
        $form = $this->createForm(AskForCodeType::class);
        $form->handleRequest($request);

        $codeForm = $this->createForm(ValidateCodeType::class);
        $codeForm->handleRequest($request);

        $resetForm = $this->createForm(ResetPasswordType::class);
        $resetForm->handleRequest($request);

        if($resetForm->isSubmitted() && $resetForm->isValid()) {
            /** @var User $user */
            $user = $this->userRepository->findOneById($resetForm->get('user')->getData());

            if(!$user) {
                $this->addFlash('error', 'Utilisateur non reconnu');
                return $this->redirectToRoute('app_reset');
            }

            $password = $this->encode->encodePassword($user, $resetForm->get('plainPassword')->getData());
            $user->setPassword($password);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Votre mot de passe a été mis à jour');
            return $this->redirectToRoute('app_login');
        }

        if($codeForm->isSubmitted() && $codeForm->isValid()) {
            $sentCode = $codeForm->get('code')->getData();
            $email = $codeForm->get('email')->getData();

            $user = $this->userRepository->findOneByEmail($email);
            if (!$user) {
                return $this->redirectToRoute('app_logout');
            }

            $sessionCode = $this->session->get($email);

            if($sessionCode && password_verify($sentCode, $sessionCode)) {
                $resetForm->get('user')->setData($user->getId());
                return $this->render('security/reset.html.twig',
                    ['form' => $resetForm->createView()]
                );
            }

            $this->addFlash('error', 'Code erronné');
        }

        if($form->isSubmitted()) {

            $tries = $this->session->get('tries-reset') ?? 0;
            $this->session->set('tries-reset', ++$tries);
            if($tries >= 4) {
                $this->addFlash('error', 'Trop d\'essais');
                $this->session->remove('tries-reset');
                return $this->redirectToRoute('app_logout');
            }

            if($form->isValid()) {

                $email = $form->get('email')->getData();
                $user = $this->userRepository->findOneByEmail($email);
                if (!$user) {
                    $this->addFlash('error', 'Email non reconnu');
                    return $this->redirectToRoute('app_reset');
                }
                $code = CodeGenerator::generateCode();

                $this->session->set($email, password_hash($code, PASSWORD_DEFAULT));

                $codeForm->get('email')->setData($email);

                $email = (new Email())
                    ->from($_SERVER['MAILER_FROM_ADDRESS'])
                    ->to($email)
                    ->subject('Demande de renouvellement de mot de passe')
                    ->html('<p>Vous avez demandé à réinitialiser votre mot de passe easy-auto. Voici votre code de validation</p><h3><strong>' . $code . '</strong></h3>'
                    );

                $mailer->send($email);

                return $this->render('security/reset.html.twig',
                    ['form' => $codeForm->createView()]
                );
            }


        }


        return $this->render('security/reset.html.twig',
            ['form' => $form->createView()]
        );

    }
}
