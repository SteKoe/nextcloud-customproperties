<?php

namespace OCA\CustomProperties\Service;

use OC\Files\Filesystem;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\PropertiesMapper;

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

    public function __construct(
        CustomPropertiesMapper $customPropertiesMapper,
        PropertiesMapper $propertiesMapper
    )
    {
        $this->customPropertiesMapper = $customPropertiesMapper;
        $this->propertiesMapper = $propertiesMapper;
    }

    private static function normalizeProperty($prop)
    {
        $propertyname = str_replace(self::NS_PREFIX, "", $prop->getPropertyname());
        $propertyvalue = isset($prop->propertyvalue) ? $prop->propertyvalue : "";
        $propertylabel = isset($prop->propertylabel) ? $prop->propertylabel : $propertyname;

        return array(
            "propertyname" => $propertyname,
            "propertyvalue" => $propertyvalue,
            "propertylabel" => $propertylabel
        );
    }

    function getProperties($userId, $path, $name)
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

    private static function merge_properties(array $customProperties, array $properties)
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

    private static function find_property_with_name(array $arr, string $name)
    {
        foreach ($arr as $idx => $prop) {
            if ($prop['propertyname'] === $name) {
                return $idx;
            }
        }

        return -1;
    }
}