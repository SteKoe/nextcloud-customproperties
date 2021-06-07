<?php
namespace OCA\CustomProperties\Plugin;

use OCA\CustomProperties\Db\Property;
use OCP\Files\Node;

class CustomPropertySearchResult
{
    public Node $node;
    public Property $property;

    public function __construct(Node $node, Property $property)
    {
        $this->node = $node;
        $this->property = $property;
    }
}
