<?php

namespace OCA\CustomProperties\Service;

use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Db\PropertiesMapper;
use OCA\CustomProperties\Db\Property;
use OCP\AppFramework\Db\Entity;
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

    public function __construct(
        CustomPropertiesMapper $customPropertiesMapper,
        PropertiesMapper $propertiesMapper
    )
    {
        $this->customPropertiesMapper = $customPropertiesMapper;
        $this->propertiesMapper = $propertiesMapper;
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

    public function deleteProperty(string $propertypath, string $propertyname, string $userid): Entity
    {
        $res = $this->propertiesMapper->findByPathAndName($propertypath, $propertyname, $userid);
        return $this->propertiesMapper->delete($res);
    }

    /**
     * @return CustomProperty[]
     */
    public function findCustomPropertyDefinitions(): array
    {
        return $this->customPropertiesMapper->findAll();
    }
}
