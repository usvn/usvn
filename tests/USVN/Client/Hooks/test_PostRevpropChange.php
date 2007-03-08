<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/PostRevpropChange.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class TestPostRevpropChange extends HookTest
{
    public function setUp()
    {
        parent::setUp();
        $this->hook = new USVN_Client_Hooks_PostRevpropChange('tests/tmp/testrepository', 1, "test", "svn:log", "M", "message de log");
		$this->setHttp();
    }

    public function test_postRevpropChange()
    {
		$this->hook->send();
        $request  = $this->hook->getLastRequest();
        $this->assertEquals('usvn.client.hooks.postRevpropChange', $request->getMethod());
        $this->assertSame(array('007', 1, "test", "svn:log", "M", "message de log"), $request->getParams());
    }
}