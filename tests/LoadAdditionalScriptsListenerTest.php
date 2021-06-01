<?php

namespace OCA\CustomProperties\Listener;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use PHPUnit\Framework\TestCase;

class LoadAdditionalScriptsListenerTest extends TestCase
{
    public function testLoadAdditionScripts()
    {
        $listener = $this->getMockBuilder(LoadAdditionalScriptsListener::class)
            ->onlyMethods(['addScript'])
            ->getMock();

        $listener->expects($this->exactly(1))
            ->method('addScript');

        $listener->handle(new LoadAdditionalScriptsEvent());
    }
}
