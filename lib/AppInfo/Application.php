<?php

namespace OCA\CustomProperties\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\QueryException;
use OCP\Util;

class Application extends App
{
    const APP_NAME = 'customproperties';

    /**
     * Application constructor.
     *
     * @param array $params
     * @throws QueryException
     */
    public function __construct(array $params = [])
    {
        parent::__construct(self::APP_NAME, $params);

        $container = $this->getContainer();
        $server = $container->getServer();
        $eventDispatcher = $server->getEventDispatcher();

        $eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function () {
            Util::addStyle('customproperties', 'sidebartab');
            Util::addScript('customproperties', 'sidebartab');
        });
    }
}
