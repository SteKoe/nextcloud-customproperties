<?php

namespace OCA\CustomProperties\Plugin;

use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Service\PropertyService;
use Sabre\DAV\INode;
use Sabre\DAV\PropFind;
use Sabre\DAV\PropPatch;
use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;

class SabreServerPlugin extends ServerPlugin
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

    public function __construct(PropertyService $propertyService, $userId)
    {
        $this->propertyService = $propertyService;
        $this->userId = $userId;
        $this->customPropertyDefinitions = $this->propertyService->findCustomPropertyDefinitions();
    }

    private function getCustomPropertynames()
    {
        return array_map(function (CustomProperty $customProperty) {
            return "{" . Application::NS_PREFIX_CUSTOMPROPERTIES . "}" . $customProperty->propertyname;
        }, $this->customPropertyDefinitions);
    }

    public function initialize(Server $server)
    {
        $this->server = $server;
        $this->server->xml->namespaceMap[Application::NS_PREFIX_CUSTOMPROPERTIES] = 'cp';

        $this->server->on('propFind', [$this, 'propFind']);
        $this->server->on('propPatch', [$this, 'propPatch']);
    }

    /**
     * @param PropFind $propFind
     * @param INode $node
     * @return void
     */
    public function propFind(PropFind $propFind, INode $path)
    {
        var_dump(get_class($path));
//        $customPropertynames = $this->getCustomPropertynames();
//        if ($propFind->isAllProps()) {
//            foreach ($customPropertynames as $propertyname) {
//                $entity = $this->propertyService->getCustomProperty($path->getName(), $propertyname, 0);
//
//                $value = $entity === null ? null : $entity->propertyvalue;
//                $status = $entity === null ? 404 : 200;
//
//                var_dump(($path->, $propertyname, $value, $status);
//                $propFind->set($propertyname, $value, $status);
//            }
//        } else {
//            foreach ($customPropertynames as $propertyname) {
//                $propFind->handle($propertyname, function () use ($path, $propertyname) {
//                    return $this->propertyService->getCustomProperty($path->getName(), $propertyname, 0);
//                });
//            }
//        }
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
                    $this->propertyService->upsertProperty($path, $key, $value, $this->userId);
                }
                return true;
            } catch (\Exception $e) {
                return false;
            }
        });
    }
}
