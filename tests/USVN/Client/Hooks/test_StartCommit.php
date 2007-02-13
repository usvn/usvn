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
        $this->hook->setHttpClient($this->httpClient);
    }

    public function test_startCommit()
    {
        $this->setServerResponseTo(true);
        $this->assertTrue($this->hook->send());
        $this->setServerResponseTo(false);
        $this->assertFalse($this->hook->send());
        $request  = $this->hook->getLastRequest();
        $this->assertSame('usvn.client.hooks.startCommit', $request->getMethod());
        $this->assertSame(array('tests/tmp/testrepository/', 'toto'), $request->getParams());
    }
}