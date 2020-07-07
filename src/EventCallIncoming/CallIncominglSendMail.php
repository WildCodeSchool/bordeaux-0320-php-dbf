<?php


namespace App\EventCallIncoming;

use App\Entity\User;
use App\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class CallIncominglSendMail implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    private $mailer;
    private $sender;

    public function __construct(Mailer $mailer, $sender)
    {
        // On injecte notre expediteur et la classe pour envoyer des mails
        $this->mailer = $mailer;
        $this->sender = $sender;
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
        /** @var User $user */
        $user = $event->getSubject();

        $subject = "Un appel a été ajouté";
        $email = (new Email())
            ->from()
            ->to($user->getEmail())
            ->subject($subject)
            ->html($this->renderView);

        $this->mailer->send($email);
    }
}
