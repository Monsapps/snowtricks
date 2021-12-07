<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Event\ConfirmEmailEvent;

class ConfirmMailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ConfirmEmailEvent::NAME => "sendEmail"
        ];
    }

    public function sendEmail(ConfirmEmailEvent $event)
    {
        $event->sendConfirmationEmail();
    }
}