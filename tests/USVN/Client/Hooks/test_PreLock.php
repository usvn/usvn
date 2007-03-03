<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/PreLock.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class TestPreLock extends HookTest
{
    public function setUp()
    {
        $this->hook = new USVN_Client_Hooks_PreLock('tests/tmp/testrepository', 'titi', 'test');
        parent::setUp();
    }

    public function test_preLock()
    {
        $this->setServerResponseTo(0);
        $this->assertEquals(0, $this->hook->send());
        $this->setServerResponseTo("Lock error");
        $this->assertEquals("Lock error", $this->hook->send());
        $request  = $this->hook->getLastRequest();
        $this->assertEquals('usvn.client.hooks.preLock', $request->getMethod());
        $this->assertSame(array('007', 'titi', 'test'), $request->getParams());
    }
}