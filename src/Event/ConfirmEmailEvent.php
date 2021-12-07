<?php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ConfirmEmailEvent extends Event
{
    const NAME = "confirm.email";

    private $email;
    private $name;
    private $token;
    private $request;
    private $mailer;

    public function __construct(
        string $email,
        string $name, 
        string $token,
        Request $request,
        MailerInterface $mailer)
    {
        $this->email = $email;
        $this->name = $name;
        $this->token = $token;
        $this->request = $request;
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail()
    {
        $baseUrl = $this->request->getSchemeAndHttpHost();

        $email = (new Email())
            ->to($this->email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("Welcome ". $this->name ." to Snowtricks community!")
            ->html("
                <p>Welcome {$this->name} to Snowtricks community!</p>
                <p>You need to confirm your email with this link: <a href=\"{$baseUrl}/confirm/{$this->token}\">here</a></p>
                <p>Thanks!</p>");
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // TODO Manage error Redirect to error page
        }
    }
}