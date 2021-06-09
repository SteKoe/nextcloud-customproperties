<?php

namespace OCA\CustomProperties\Listener;

use OCA\CustomProperties\Plugin\CustomPropertiesSabreServerPlugin;
use OCA\CustomProperties\Service\PropertyService;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\SabrePluginEvent;

class SabreAddPluginListener implements IEventListener
{
    /**
     * @var PropertyService
     */
    private $propertyService;

    /**
     * SabreAddPluginListener constructor.
     *
     * @param PropertyService $propertyService
     */
    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function handle(Event $event): void
    {
        if ($event instanceof SabrePluginEvent) {
            $server = $event->getServer();
            $server->addPlugin(new CustomPropertiesSabreServerPlugin(
                $this->propertyService,
                \OC_User::getUser()
            ));
        }
    }
}
