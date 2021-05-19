<?php

namespace OCA\CustomProperties\Plugin;

use Sabre\DAV\INode;
use Sabre\DAV\PropFind;
use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;

class SabreServerPlugin extends ServerPlugin
{
    const NS_CUSTOMPROPERTIES = 'http://customproperties.nextcloud.com/ns/';

    public function initialize(Server $server)
    {
        $server->xml->namespaceMap[self::NS_CUSTOMPROPERTIES] = 'cp';
        $server->on('propFind', [$this, 'propFind']);
    }

    /**
     * @param PropFind $propFind
     * @param INode $node
     * @return void
     */
    public function propFind(PropFind $propFind, INode $node)
    {
        $propertyName = '{' . self::NS_CUSTOMPROPERTIES . '}abc';
        if ($propFind->isAllProps()) {
            $propFind->set($propertyName, 'find all props');
        } else {
            $propFind->handle($propertyName, function () {
                return "find single prop";
            });
        }
    }
}
