<?php


namespace App\EventCallIncoming;

use App\Entity\Call;
use App\Events;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\LogicException;
use Twig\Environment;

class CallIncomingSendMail implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    private $mailer;
    private $sender;
    private $templating;
    private $session;


    public function __construct(MailerInterface $mailer, $sender, Environment $twig, SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->templating = $twig;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::CALL_INCOMING => 'callIncoming',
        ];
    }

    public function callIncoming(GenericEvent $event): void
    {
        /** @var Call $call */
        $call = $event->getSubject();
        $recipient = $call->getRecipient();
        $subject = "EASY-AUTO - Demande de rappel";


        if('true' === $_SERVER['SEND_EMAILS']) {
            if (null !== $recipient) {
                $email = (new TemplatedEmail())
                    ->from($this->sender)
                    ->to($recipient->getEmail())
                    ->subject($subject)
                    ->htmlTemplate('call/mail/template_mail.html.twig')
                    ->context([
                        'call' => $call,
                    ]);

                if ($call->getIsUrgent() || $recipient->getHasAcceptedAlert()) {
                    try {
                        $this->mailer->send($email);
                    } catch (\Exception $e) {
                        $this->session->getFlashBag()->add('error', "Erreur lors de l'envoi du mail");
                    }
                }
            } else {
                $collaborators = $call->getService()->getUsers();
                foreach ($collaborators as $collaborator) {
                    if ($collaborator->getCanBeRecipient()) {
                        $email = (new TemplatedEmail())
                            ->from($this->sender)
                            ->to($collaborator->getEmail())
                            ->subject($subject)
                            ->htmlTemplate('call/mail/template_mail.html.twig')
                            ->context([
                                'call' => $call,
                            ]);
                        if ($call->getIsUrgent() || $collaborator->getHasAcceptedAlert()) {
                            try {
                                $this->mailer->send($email);
                            } catch (\Exception $e) {
                                $this->session->getFlashBag()->add('error', "Erreur lors de l'envoi du mail");
                            }
                        }
                    }
                }
            }
        }
    }
}
