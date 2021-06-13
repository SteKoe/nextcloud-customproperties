<?php

namespace OCA\CustomProperties\Plugin;

use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Db\Property;
use OCA\CustomProperties\Service\PropertyService;
use OCA\DAV\Connector\Sabre\Node;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\PropFind;
use Sabre\DAV\PropPatch;
use Sabre\DAV\Server;
use Sabre\DAV\Tree;

class CustomPropertiesSabreServerPluginTest extends TestCase
{
    /**
     * @var CustomPropertiesSabreServerPlugin
     */
    private $plugin;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var PropertyService
     */
    private $propertyService;

    protected function setUp(): void
    {
        $customProperty = $this->createCustomProperty();
        $property = $this->createProperty();

        $this->createMocks($customProperty, $property);
    }

    function testPropPatchCallsMethodToStorePropertyInDatabase()
    {
        $path = "example.txt";
        $propertyvalue = "propertyname";
        $propertyname = "{" . Application::NAMESPACE_URL . "}propertyname";

        $this->propertyService->expects($this->exactly(1))
            ->method('upsertProperty')
            ->with(
                $this->equalTo($path),
                $this->equalTo($propertyname),
                $this->equalTo($propertyvalue),
                $this->equalTo("4711")
            );

        $propPatch = new PropPatch([$propertyname => $propertyvalue]);
        $this->plugin->propPatch($path, $propPatch);
        $propPatch->commit();
    }

    function testPropPatchCallsMethodToDeletePropertyFromDatabaseWhenEmptyValueIsSet()
    {
        $path = "example.txt";
        $propertyvalue = "   ";
        $propertyname = "{" . Application::NAMESPACE_URL . "}propertyname";

        $this->propertyService->expects($this->exactly(1))
            ->method('deleteProperty')
            ->with(
                $this->equalTo($path),
                $this->equalTo($propertyname),
                $this->equalTo("4711")
            );

        $propPatch = new PropPatch([$propertyname => $propertyvalue]);
        $this->plugin->propPatch($path, $propPatch);
        $propPatch->commit();
    }


    function testPropPatchIsNotExecutedWhenFileDoesNotExist()
    {
        $this->expectException(NotFound::class);
        $this->propertyService->expects($this->exactly(0))
            ->method('upsertProperty');

        $this->server->tree->method('getNodeForPath')
            ->with('fileDoesNotExits.txt')
            ->willThrowException(new NotFound());

        $propPatch = new PropPatch([]);
        $this->plugin->propPatch("fileDoesNotExits.txt", $propPatch);
        $propPatch->commit();
    }

    function testPropPatchIsNotExecutedWhenPathIsNotOfTypeINode()
    {
        $this->server->tree->method('getNodeForPath')
            ->willReturn("not an INode");

        $this->propertyService->expects($this->exactly(0))
            ->method('upsertProperty');

        $propPatch = new PropPatch([]);
        $this->plugin->propPatch("whatever", $propPatch);
        $propPatch->commit();
    }

    function testPropFindAllPropsForCustomPropertyHavingAValue()
    {
        $propertyname = CustomProperty::withNamespaceURI("propertyname");

        $propFind = new PropFind("example.txt", [], 0, PropFind::ALLPROPS);
        $this->plugin->propFind($propFind, $this->createMock(Node::class));

        $this->assertEquals("200", $propFind->getStatus($propertyname));
        $this->assertEquals("value", $propFind->get($propertyname));
    }

    function testPropFindAllPropsForCustomPropertyNotFound()
    {
        $propertyname = CustomProperty::withNamespaceURI("iamnotexisting");

        $propFind = new PropFind("example.txt", [], 0, PropFind::ALLPROPS);
        $this->plugin->propFind($propFind, $this->createMock(Node::class));

        $this->assertEquals(null, $propFind->get($propertyname));
        $this->assertEquals(null, $propFind->getStatus($propertyname));
    }

    function testPropFindAllPropsForCustomPropertyHavingNoValue()
    {
        $this->createMocks($this->createCustomProperty(), null);

        $propertyname = CustomProperty::withNamespaceURI("propertyname");

        $propFind = new PropFind("example.txt", [], 0, PropFind::ALLPROPS);
        $this->plugin->propFind($propFind, $this->createMock(Node::class));

        $this->assertEquals(null, $propFind->get($propertyname));
        $this->assertEquals("404", $propFind->getStatus($propertyname));
    }

    function testPropFindForCustomPropertyHavingAValue()
    {
        $propertyname = CustomProperty::withNamespaceURI("propertyname");

        $propFind = new PropFind("example.txt", [$propertyname]);
        $this->plugin->propFind($propFind, $this->createMock(Node::class));

        $this->assertEquals("200", $propFind->getStatus($propertyname));
        $this->assertEquals($this->createProperty(), $propFind->get($propertyname));
    }

    function testWriterCanWriteProperties() {
        $writer = $this->server->xml->getWriter();
        $writer->openMemory();

        $this->assertNotNull($writer->writeElement("test", self::createProperty()));
    }

    /**
     * @param CustomProperty $customProperty
     * @throws \Sabre\DAV\Exception
     */
    private function createMocks(CustomProperty $customProperty, ?Property $property): void
    {
        $this->propertyService = $this->createMock(PropertyService::class);
        $this->propertyService->method('findCustomPropertyDefinitions')
            ->willReturn([$customProperty]);
        $this->propertyService->method('getCustomProperty')
            ->willReturn($property);

        $this->plugin = new CustomPropertiesSabreServerPlugin($this->propertyService, 4711, $this->createMock(LoggerInterface::class));

        $tree = $this->createMock(Tree::class);
        $node = $this->getMockBuilder(Node::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tree->method('getNodeForPath')
            ->with($this->anything())
            ->willReturn($node);
        $this->server = $this->getMockBuilder(Server::class)
            ->setConstructorArgs([$tree])
            ->getMock();

        $this->plugin->initialize($this->server);
    }

    /**
     * @return CustomProperty
     */
    private function createCustomProperty(): CustomProperty
    {
        $customProperty = new CustomProperty();
        $customProperty->id = 1;
        $customProperty->userId = null;
        $customProperty->propertyname = "propertyname";
        $customProperty->propertylabel = "propertylabel";
        return $customProperty;
    }

    /**
     * @return Property
     */
    private function createProperty(): Property
    {
        $property = new Property();
        $property->propertyvalue = "value";
        return $property;
    }
}
