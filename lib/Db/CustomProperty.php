<?php

namespace OCA\CustomProperties\Db;

use OCP\AppFramework\Db\Entity;

class CustomProperty extends Entity
{
    /** @var string */
    public $userId;
    /** @var string */
    public $propertyname;
    /** @var string */
    public $propertylabel;
    /** @var string */
    public $propertytype;

    public function __construct()
    {
        $this->addType('id', 'int');
        $this->addType('userId', 'string');
        $this->addType('propertyname', 'string');
        $this->addType('propertylabel', 'string');
        $this->addType('propertytype', 'string');
    }

    public static function createSlug($propertylabel)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '', $propertylabel)));
    }
}
