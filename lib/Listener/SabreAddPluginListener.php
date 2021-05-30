<?php

namespace OCA\CustomProperties\Listener;

use OCA\CustomProperties\Plugin\SabreServerPlugin;
use OCA\CustomProperties\Service\PropertyService;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\SabrePluginEvent;
use Psr\Log\LoggerInterface;

class SabreAddPluginListener implements IEventListener
{
    /**
     * @var PropertyService
     */
    private $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function handle(Event $event): void
    {
        if ($event instanceof SabrePluginEvent) {
            $event->getServer()->addPlugin(new SabreServerPlugin(
                $this->propertyService,
                \OC_User::getUser()
            ));
        }
    }
}
