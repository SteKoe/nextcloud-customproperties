<?php

namespace OCA\CustomProperties\Plugin;

use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Db\Property;
use OCA\CustomProperties\Service\PropertyService;
use PHPUnit\Framework\TestCase;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\PropFind;
use Sabre\DAV\PropPatch;
use Sabre\DAV\Server;
use Sabre\DAV\SimpleCollection;
use Sabre\DAV\SimpleFile;
use Sabre\DAV\Tree;

class SabreServerPluginTest extends TestCase
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
    /**
     * @var CustomProperty
     */
    private $customProperty;
    /**
     * @var Property
     */
    private $property;

    protected function setUp(): void
    {
        $this->customProperty = new CustomProperty();
        $this->customProperty->id = 1;
        $this->customProperty->userId = null;
        $this->customProperty->propertyname = "propertyname";
        $this->customProperty->propertylabel = "propertylabel";

        $this->property = new Property();
        $this->property->propertyvalue = "value";

        $this->createMocks($this->customProperty, $this->property);
    }

    function testPluginRegistersNamespaceForCustomPropertiesAtServer()
    {
        $this->assertArrayHasKey(Application::NS_PREFIX_CUSTOMPROPERTIES, $this->server->xml->namespaceMap);
        $this->assertEquals($this->server->xml->namespaceMap[Application::NS_PREFIX_CUSTOMPROPERTIES], "cp");
    }

    function testPropPatchCallsMethodToStorePropertyInDatabase()
    {
        $path = "example.txt";
        $propertyvalue = "propertyname";
        $propertyname = "{" . Application::NS_PREFIX_CUSTOMPROPERTIES . "}propertyname";

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

    function testPropPatchIsNotExecutedWhenFileDoesNotExist()
    {
        $this->expectException(NotFound::class);
        $this->propertyService->expects($this->exactly(0))
            ->method('upsertProperty');

        $path = "fileDoesNotExits.txt";
        $propPatch = new PropPatch([]);
        $this->plugin->propPatch($path, $propPatch);
        $propPatch->commit();
    }

    function testPropPatchIsNotExecutedWhenPathIsNotOfTypeINode()
    {
        $this->server->tree = $this->getMockBuilder(Tree::class)
            ->setConstructorArgs([new SimpleCollection("root")])
            ->onlyMethods(['getNodeForPath'])
            ->getMock();

        $this->server->tree->method('getNodeForPath')
            ->willReturn("not an INode");

        $this->propertyService->expects($this->exactly(0))
            ->method('upsertProperty');

        $path = "fileDoesNotExits.txt";
        $propPatch = new PropPatch([]);
        $this->plugin->propPatch($path, $propPatch);
        $propPatch->commit();
    }

    function testPropFindAllPropsForCustomPropertyHavingAValue()
    {
        $path = "example.txt";
        $propertyname = "{" . Application::NS_PREFIX_CUSTOMPROPERTIES . "}propertyname";

        $inode = $this->server->tree->getNodeForPath($path);

        $propFind = new PropFind($path, [], 0, PropFind::ALLPROPS);
        $this->plugin->propFind($propFind, $inode);

        $this->assertEquals("200", $propFind->getStatus($propertyname));
        $this->assertEquals("value", $propFind->get($propertyname));
    }

    function testPropFindAllPropsForCustomPropertyNotFound()
    {
        $path = "example.txt";
        $propertyname = "{" . Application::NS_PREFIX_CUSTOMPROPERTIES . "}iamnotexisting";

        $inode = $this->server->tree->getNodeForPath($path);

        $propFind = new PropFind($path, [], 0, PropFind::ALLPROPS);
        $this->plugin->propFind($propFind, $inode);

        $this->assertEquals(null, $propFind->get($propertyname));
        $this->assertEquals(null, $propFind->getStatus($propertyname));
    }

    function testPropFindAllPropsForCustomPropertyHavingNoValue()
    {
        $this->createMocks($this->customProperty, null);

        $path = "example.txt";
        $propertyname = "{" . Application::NS_PREFIX_CUSTOMPROPERTIES . "}propertyname";

        $inode = $this->server->tree->getNodeForPath($path);

        $propFind = new PropFind($path, [], 0, PropFind::ALLPROPS);
        $this->plugin->propFind($propFind, $inode);

        $this->assertEquals(null, $propFind->get($propertyname));
        $this->assertEquals("404", $propFind->getStatus($propertyname));
    }

    function testPropFindForCustomPropertyHavingAValue()
    {
        $path = "example.txt";
        $propertyname = "{" . Application::NS_PREFIX_CUSTOMPROPERTIES . "}propertyname";

        $inode = $this->server->tree->getNodeForPath($path);

        $propFind = new PropFind($path, [$propertyname]);
        $this->plugin->propFind($propFind, $inode);

        $this->assertEquals("200", $propFind->getStatus($propertyname));
        $this->assertEquals($this->property, $propFind->get($propertyname));
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

        $this->plugin = new CustomPropertiesSabreServerPlugin($this->propertyService, 4711);
        $root = new SimpleCollection('root', [new SimpleFile("example.txt", "example content", "plain/text")]);
        $this->server = new Server(new Tree($root));

        $this->plugin->initialize($this->server);
    }
}
