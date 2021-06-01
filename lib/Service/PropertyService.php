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
    /**
     * @var LoggerInterface
     */
    private $logger;
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

    private static function normalizeProperty($prop): array
    {
        $propertyname = str_replace(Application::NS_PREFIX_CUSTOMPROPERTIES, "", $prop->getPropertyname());
        $propertyvalue = $prop->propertyvalue ?? "";
        $propertylabel = $prop->propertylabel ?? $propertyname;

        return array(
            "propertyname" => $propertyname,
            "propertyvalue" => $propertyvalue,
            "propertylabel" => $propertylabel
        );
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

    function getProperties($userId, $path, $name): array
    {
        $customProperties = $this->customPropertiesMapper->findAll();
        $string = Filesystem::normalizePath("files/$userId/$path/$name");
        $propertypath = ltrim($string, "/");
        $properties = $this->propertiesMapper->findAllByPath($propertypath, $userId);

        // Normalize the properties
        $customProperties = array_map('self::normalizeProperty', $customProperties);
        $properties = array_map('self::normalizeProperty', $properties);
        $abc = $this->server->getPropertiesIteratorForPath("/files/admin/$name");

        $mergedProperties = self::merge_properties($customProperties, $properties);
        foreach ($mergedProperties as $key => $mergedProperty) {
            $mergedProperties[$key]['_knownproperty'] = self::find_property_with_name($customProperties, $mergedProperty['propertyname']) !== -1;
        }
        return $mergedProperties;
    }

    private static function merge_properties(array $customProperties, array $properties): array
    {
        $result = $customProperties;

        foreach ($properties as $customProperty) {
            $index = self::find_property_with_name($result, $customProperty['propertyname']);
            if ($index === -1) {
                $result[] = $customProperty;
            } else {
                $result[$index]['propertyvalue'] = $customProperty['propertyvalue'];
            }
        }

        return $result;
    }

    private static function find_property_with_name(array $arr, string $name): int
    {
        foreach ($arr as $idx => $prop) {
            if ($prop['propertyname'] === $name) {
                return $idx;
            }
        }

        return -1;
    }

    public function handleRename(Node $source, Node $target)
    {
        $oldPath = sprintf("%s/%s", "", $source->getInternalPath());
        $newPath = sprintf("%s/%s", "", $target->getInternalPath());

        // files/admin/Documents
        // files/admin/b.md
        // files/admin/Photos/Birdie.jpg
        // files/admin/a.pdf
        $userId = $target->getOwner()->getUID();
        $name = $source->getName();
        $propertypath = ltrim(Filesystem::normalizePath("files/$userId/???/$name"), "/");

        $log = [
            sprintf("Renaming properties from %s to %s", $oldPath, $newPath),
            sprintf("getPath            : %s", $source->getPath()),
            sprintf("getInternalPath    : %s", $source->getInternalPath()),
            sprintf("propertypath       : %s", $propertypath)
        ];

        $this->logger->info(implode("<br>\n", $log));
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
