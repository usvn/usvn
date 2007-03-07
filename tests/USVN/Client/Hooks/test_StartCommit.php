<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/StartCommit.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class TestStartCommit extends HookTest
{
    public function setUp()
    {
        parent::setUp();
        $this->hook = new USVN_Client_Hooks_StartCommit('tests/tmp/testrepository', 'toto');
		$this->setHttp();
    }

    public function test_startCommit()
    {
        $this->setServerResponseTo(0);
        $this->assertEquals(0, $this->hook->send());
        $this->setServerResponseTo("Start commit error");
        $this->assertEquals("Start commit error", $this->hook->send());
        $request  = $this->hook->getLastRequest();
        $this->assertSame('usvn.client.hooks.startCommit', $request->getMethod());
        $this->assertSame(array('007', 'toto'), $request->getParams());
    }
}