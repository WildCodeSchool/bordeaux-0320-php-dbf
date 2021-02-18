<?php


namespace App\Command\Service;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class RgpdCommandMailer
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($results, $refDate)
    {
        $today = new \DateTime('now');

        $email = (new TemplatedEmail())
            ->from($_SERVER['MAILER_FROM_ADDRESS'])
            ->to($_SERVER['REPORT_DESTINATARY'])
            ->subject('Rapport de maintenance RGPD de la base Easy Auto')
            ->htmlTemplate('emails/template_rgpd.html.twig')
            ->context([
                'date' => $today,
                'data' => $results,
                'ref'  => $refDate
            ]);

        $this->mailer->send($email);
    }

}
