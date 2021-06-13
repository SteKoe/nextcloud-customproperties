<?php

namespace OCA\CustomProperties\Plugin;

use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Db\Property;
use OCA\CustomProperties\Service\PropertyService;
use OCA\DAV\Connector\Sabre\Node;
use Sabre\DAV\INode;
use Sabre\DAV\PropFind;
use Sabre\DAV\PropPatch;
use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;
use Sabre\Xml\Writer;

class CustomPropertiesSabreServerPlugin extends ServerPlugin
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var PropertyService
     */
    private $propertyService;

    private $userId;
    /**
     * @var CustomProperty[]
     */
    private $customPropertyDefinitions;

    /**
     * CustomPropertiesSabreServerPlugin constructor.
     * @param PropertyService $propertyService
     * @param $userId
     */
    public function __construct(PropertyService $propertyService, $userId)
    {
        $this->propertyService = $propertyService;
        $this->userId = $userId;

        $this->customPropertyDefinitions = $this->propertyService->findCustomPropertyDefinitions();
    }

    /**
     * @param Server $server
     */
    public function initialize(Server $server)
    {
        $this->server = $server;

        $this->server->xml->classMap[Property::class] = function (Writer $writer, Property $value) {
            $writer->write($value->propertyvalue);
        };

        $this->server->on('propFind', [$this, 'propFind']);
        $this->server->on('propPatch', [$this, 'propPatch']);
    }

    private function getCustomPropertynames(): array
    {
        return array_map(function (CustomProperty $customProperty) {
            return "{" . Application::NAMESPACE_URL . "}" . $customProperty->propertyname;
        }, $this->customPropertyDefinitions);
    }

    /**
     * @param PropFind $propFind
     * @param INode $node
     * @return void
     */
    public function propFind(PropFind $propFind, INode $node)
    {
        if ($node instanceof Node) {
            $path = "files" . DIRECTORY_SEPARATOR . \OC_User::getUser() . $node->getPath();

            if ($propFind->isAllProps()) {
                $this->handlePropFindAllProps($propFind, $path);
            } else {
                $this->handlePropFind($propFind, $path);
            }
        }
    }

    /**
     * Handle PROPPATCH WebDav requests
     *
     * @param $path
     * @param PropPatch $propPatch
     * @throws \Sabre\DAV\Exception\NotFound
     */
    public function propPatch($path, PropPatch $propPatch)
    {
        $node = $this->server->tree->getNodeForPath($path);

        if (!($node instanceof INode)) {
            return;
        }

        $propPatch->handle($this->getCustomPropertynames(), function ($a) use ($path) {
            try {
                foreach ($a as $key => $value) {
                    if (!empty(trim($value))) {
                        $this->propertyService->upsertProperty($path, $key, $value, $this->userId);
                    } else {
                        $this->propertyService->deleteProperty($path, $key, $this->userId);
                    }
                }
                return true;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * @param PropFind $propFind
     * @param string $path
     */
    private function handlePropFindAllProps(PropFind $propFind, string $path): void
    {
        foreach ($this->getCustomPropertynames() as $propertyname) {
            $entity = $this->propertyService->getCustomProperty($path, $propertyname, $this->userId);
            $value = $entity === null ? null : $entity->propertyvalue;

            $propFind->set($propertyname, $value);
        }
    }

    /**
     * @param PropFind $propFind
     * @param string $path
     */
    private function handlePropFind(PropFind $propFind, string $path): void
    {
        foreach ($this->getCustomPropertynames() as $propertyname) {
            $propFind->handle($propertyname, function () use ($path, $propertyname) {
                return $this->propertyService->getCustomProperty($path, $propertyname, $this->userId);
            });
        }
    }
}
