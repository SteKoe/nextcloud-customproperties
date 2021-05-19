<?php

namespace OCA\CustomProperties\Listener;

use OCA\CustomProperties\Plugin\SabreServerPlugin;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\SabrePluginEvent;

class SabreAddPluginListener implements IEventListener
{
    public function handle(Event $event): void
    {
        if ($event instanceof SabrePluginEvent) {
            $event->getServer()->addPlugin(new SabreServerPlugin());
        }
    }
}
