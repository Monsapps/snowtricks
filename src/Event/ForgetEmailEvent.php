<?php
/**
 * Forget password email event class, send an email with 
 * the token to create a new password
 */
namespace App\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\Event;

class ForgetEmailEvent extends Event
{
    const NAME = "forget.email";

    private $email;
    private $username;
    private $token;
    private $request;
    private $mailer;

    /**
     * Store required value on call
     * @return void
     */
    public function __construct(
        string $email,
        string $name, 
        string $token,
        Request $request,
        MailerInterface $mailer
    )
    {
        $this->email = $email;
        $this->username = $name;
        $this->token = $token;
        $this->request = $request;
        $this->mailer = $mailer;
    }

    /**
     * Send forgot password email
     * @return void
     */
    public function sendForgotEmail()
    {
        $baseUrl = $this->request->getSchemeAndHttpHost();

        $email = (new Email())
        ->to($this->email)
        ->subject("Snowtricks.com: Reset password request")
        ->html("
            <p>Welcome {$this->username}</p>
            <p>A password reset request has been requested, if you requested it continue reading this message. Otherwise delete this email</p>
            <p>Recreate your password by following this link: <a href=\"{$baseUrl}/reset_password/{$this->token}\">here</a></p>
            <p>Thanks!</p>");
    try {
        $this->mailer->send($email);
    } catch (TransportExceptionInterface $e) {
        // TODO Manage error Redirect to error page
    }
    }
}
