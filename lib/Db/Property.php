<?php

namespace OCA\CustomProperties\Db;

use OCP\AppFramework\Db\Entity;

class Property extends Entity
{
    /** @var string */
    public $userid;
    /** @var string */
    public $propertypath;
    /** @var string */
    public $propertyname;
    /** @var string */
    public $propertyvalue;

    public function __construct()
    {
        $this->addType('id', 'int');
        $this->addType('userid', 'string');
        $this->addType('propertypath', 'string');
        $this->addType('propertyname', 'string');
        $this->addType('propertyvalue', 'string');
    }
}