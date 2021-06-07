<?php

namespace OCA\CustomProperties\Listener;

use OCA\CustomProperties\Plugin\CustomPropertiesSabreServerPlugin;
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
    private LoggerInterface $logger;

    public function __construct(PropertyService $propertyService, LoggerInterface $logger)
    {
        $this->propertyService = $propertyService;
        $this->logger = $logger;
    }

    public function handle(Event $event): void
    {
        if ($event instanceof SabrePluginEvent) {
            $server = $event->getServer();
            $server->addPlugin(new CustomPropertiesSabreServerPlugin(
                $this->propertyService,
                \OC_User::getUser(),
                $this->logger
            ));
        }
    }
}
