<?php

namespace OCA\CustomProperties\Service;

use OC\Files\Filesystem;
use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Db\PropertiesMapper;
use OCA\CustomProperties\Db\Property;
use OCP\AppFramework\Db\Entity;
use OCP\Files\Node;
use Psr\Log\LoggerInterface;
use Sabre\DAV\Server;

class PropertyService
{
    /**
     * @var CustomPropertiesMapper
     */
    private $customPropertiesMapper;
    /**
     * @var PropertiesMapper
     */
    private $propertiesMapper;
    private LoggerInterface $logger;
    private Server $server;

    public function __construct(
        CustomPropertiesMapper $customPropertiesMapper,
        PropertiesMapper $propertiesMapper,
        Server $server,
        LoggerInterface $logger
    )
    {
        $this->customPropertiesMapper = $customPropertiesMapper;
        $this->propertiesMapper = $propertiesMapper;
        $this->logger = $logger;
        $this->server = $server;
    }

    /**
     * @param $propertypath
     * @param $propertyname
     * @param $userid
     * @return Property
     */
    function getCustomProperty(string $propertypath, string $propertyname, string $userid): ?Entity {
        return $this->propertiesMapper->findByPathAndName($propertypath, $propertyname, $userid);
    }

    /**
     * @param string $propertypath
     * @param string $propertyname
     * @param string $propertyvalue
     * @return Property|Entity
     * @throws \OCP\DB\Exception
     */
    public function upsertProperty(string $propertypath, string $propertyname, string $propertyvalue, string $userid)
    {
        $res = $this->propertiesMapper->findByPathAndName($propertypath, $propertyname, $userid);
        if (!is_null($res)) {
            $res->setPropertyvalue($propertyvalue);
            return $this->propertiesMapper->update($res);
        }

        $property = new Property();
        $property->setUserid($userid);
        $property->setPropertypath($propertypath);
        $property->setPropertyname($propertyname);
        $property->setPropertyvalue($propertyvalue);

        return $this->propertiesMapper->insert($property);
    }

    /**
     * @return CustomProperty[]
     */
    public function findCustomPropertyDefinitions(): array
    {
        return $this->customPropertiesMapper->findAll();
    }
}
