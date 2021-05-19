<?php
declare(strict_types=1);

namespace OCA\CustomProperties\Listener;

use OCA\CustomProperties\Service\PropertyService;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\Events\Node\NodeRenamedEvent;
use Psr\Log\LoggerInterface;

class FileEventsListener implements IEventListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PropertyService
     */
    private $propertyService;

    public function __construct(LoggerInterface $logger, PropertyService $propertyService)
    {
        $this->logger = $logger;
        $this->propertyService = $propertyService;
    }

    public function handle(Event $event): void
    {
        if ($event instanceof NodeDeletedEvent) {
            $this->logger->info(sprintf("NodeDeletedEvent: %s", print_r($event->getNode())));
        }
        if ($event instanceof NodeRenamedEvent) {
            $this->propertyService->handleRename($event->getSource(), $event->getTarget());
        }
    }
}
