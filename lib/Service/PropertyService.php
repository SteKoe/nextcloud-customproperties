<?php
namespace OCA\CustomProperties\Service;

use OC\Files\Filesystem;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\PropertiesMapper;
use OCP\Files\Node;
use Psr\Log\LoggerInterface;
use Sabre\DAV\Server;

class PropertyService
{
    const NS_PREFIX = "{http://owncloud.org/ns}";

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

    public function __construct(
        CustomPropertiesMapper $customPropertiesMapper,
        PropertiesMapper $propertiesMapper,
        LoggerInterface $logger,
        Server $server
    )
    {
        $this->customPropertiesMapper = $customPropertiesMapper;
        $this->propertiesMapper = $propertiesMapper;
        $this->logger = $logger;
    }

    private static function normalizeProperty($prop): array
    {
        $propertyname = str_replace(self::NS_PREFIX, "", $prop->getPropertyname());
        $propertyvalue = $prop->propertyvalue ?? "";
        $propertylabel = $prop->propertylabel ?? $propertyname;

        return array(
            "propertyname" => $propertyname,
            "propertyvalue" => $propertyvalue,
            "propertylabel" => $propertylabel
        );
    }

    function getProperties($userId, $path, $name): array
    {
        $customProperties = $this->customPropertiesMapper->findAllForUser($userId);
        $propertypath = ltrim(Filesystem::normalizePath("files/$userId/$path/$name"), "/");
        $properties = $this->propertiesMapper->findAllByPath($propertypath, $userId);

        // Normalize the properties
        $customProperties = array_map('self::normalizeProperty', $customProperties);
        $properties = array_map('self::normalizeProperty', $properties);

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

//        $properties = $this->propertiesMapper->findByPathStartsWith($oldPath);
//        foreach ($properties as $property) {
//            $property->propertypath = $newPath;
//            $this->propertiesMapper->insertOrUpdate($property);
//        }
    }
}
