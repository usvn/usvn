<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/PreUnlock.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class TestPreUnlock extends HookTest
{
    public function setUp()
    {
        parent::setUp();
        $this->hook = new USVN_Client_Hooks_PreUnlock('tests/tmp/testrepository', 'titi', 'test');
		$this->setHttp();
    }

    public function test_preLock()
    {
        $this->setServerResponseTo(0);
        $this->assertEquals(0, $this->hook->send());
        $this->setServerResponseTo("Unlock error");
        $this->assertEquals("Unlock error", $this->hook->send());
        $request  = $this->hook->getLastRequest();
        $this->assertEquals('usvn.client.hooks.preUnlock', $request->getMethod());
        $this->assertSame(array('007', 'titi', 'test'), $request->getParams());
    }
}