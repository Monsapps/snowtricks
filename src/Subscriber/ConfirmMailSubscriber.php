<?php

namespace App\Subscriber;

use App\Event\ConfirmEmailEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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