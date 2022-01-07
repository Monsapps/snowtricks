<?php
/**
 * Forget password interface
 */
namespace App\Subscriber;

use App\Event\ForgetEmailEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForgetMailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ForgetEmailEvent::NAME => "sendEmail"
        ];
    }

    public function sendEmail(ForgetEmailEvent $event)
    {
        $event->sendForgotEmail();
    }
}
