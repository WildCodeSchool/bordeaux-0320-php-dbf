<?php


namespace App\EventCallIncoming;

use App\Entity\Call;
use App\Entity\User;
use App\Events;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class CallIncomingSendMail implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    private $mailer;
    private $sender;
    private $templating;

    public function __construct(MailerInterface $mailer, $sender, Environment $twig)
    {
        // On injecte notre expediteur et la classe pour envoyer des mails
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->templating = $twig;
    }

    public static function getSubscribedEvents()
    {
        return [
            // le nom de l'event et le nom de la fonction qui sera déclenché
            Events::CALL_INCOMING => 'callIncoming',
        ];
    }
    public function callIncoming(GenericEvent $event): void
    {
        /** @var Call $call*/
        $call= $event->getSubject();
        $recipient = $call->getRecipient();

        $subject = "Un appel ajouté";
        $email = (new Email())
            ->from($this->sender)
            ->to($recipient->getEmail())
            ->subject($subject)
            ->html($this->templating->render('call/mail/notification.html.twig', ['call'=> $call]));

        if ($call->getIsUrgent() || $recipient->getHasAcceptedAlert()) {
            $this->mailer->send($email);
        }
    }
}
