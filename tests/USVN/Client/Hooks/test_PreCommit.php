<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/PreCommit.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class PreCommit_For_Test extends USVN_Client_Hooks_PreCommit
{
	protected function svnLookTransaction($command, $repository, $transaction)
	{
		switch($command) {
			case "author":
				return 'toto';
			break;

			case "log":
				return 'message de commit';
			break;

			case "changed":
				return "U tutu\nU tata\n";
			break;
		}
	}
}

class TestPreCommit extends HookTest
{
    public function setUp()
    {
        $this->hook = new PreCommit_For_Test('tests/tmp/testrepository', '1');
        parent::setUp();
    }

    public function test_preCommit()
    {
        $this->setServerResponseTo(0);
        $this->assertEquals(0, $this->hook->send());
        $this->setServerResponseTo("Commit error");
        $this->assertEquals("Commit error", $this->hook->send());
        $request  = $this->hook->getLastRequest();
        $this->assertEquals('usvn.client.hooks.preCommit', $request->getMethod());
        $this->assertSame(array('007', 'toto', 'message de commit', array(array('U', 'tutu'), array('U', 'tata'))), $request->getParams());
    }
}