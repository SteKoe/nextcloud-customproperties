<?php

namespace OCA\CustomProperties\Db;

use OCA\CustomProperties\AppInfo\Application;
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

    /**
     * @var string
     */
    public $prefix = Application::NAMESPACE_PREFIX;

    public function __construct()
    {
        $this->addType('id', 'int');
        $this->addType('userId', 'string');
        $this->addType('propertyname', 'string');
        $this->addType('propertylabel', 'string');
        $this->addType('propertytype', 'string');
    }

    public static function withNamespaceURI($propertyname): string
    {
        return "{" . Application::NAMESPACE_URL . "}" . $propertyname;
    }

    public function isValid(): bool
    {
        return ($this->propertyname !== null) && (preg_match('/^[a-z]{1}[a-z0-9]*$/', $this->propertyname) === 1);
    }
}
