<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/PreRevpropChange.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class TestPreRevpropChange extends HookTest
{
    public function setUp()
    {
        parent::setUp();
        $this->hook = new USVN_Client_Hooks_PreRevpropChange('tests/tmp/testrepository', 1, "test", "svn:log", "M", "message de log");
		$this->setHttp();
    }

    public function test_PreRevpropChange()
    {
        $this->setServerResponseTo(0);
        $this->assertEquals(0, $this->hook->send());
        $this->setServerResponseTo("Change error");
        $this->assertEquals("Change error", $this->hook->send());
        $request  = $this->hook->getLastRequest();
        $this->assertEquals('usvn.client.hooks.preRevpropChange', $request->getMethod());
        $this->assertSame(array('007', 1, "test", "svn:log", "M", "message de log"), $request->getParams());
    }
}