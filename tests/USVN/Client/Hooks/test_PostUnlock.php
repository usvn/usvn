<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/PostUnlock.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class TestPostUnlock extends HookTest
{
    public function setUp()
    {
        parent::setUp();
        $this->hook = new USVN_Client_Hooks_PostUnlock('tests/tmp/testrepository', 'titi', 'test');
		$this->setHttp();
    }

    public function test_postUnlock()
    {
		$this->setServerResponseTo(0);
		$this->hook->send();
        $request  = $this->hook->getLastRequest();
        $this->assertEquals('usvn.client.hooks.postUnlock', $request->getMethod());
        $this->assertSame(array('007', 'titi', 'test'), $request->getParams());
    }
}