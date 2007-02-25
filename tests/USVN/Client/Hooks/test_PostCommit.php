<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/PostCommit.php';
require_once 'tests/USVN/Client/Hooks/HookTest.php';

class PostCommit_For_Test extends USVN_Client_Hooks_PostCommit
{
	protected function svnLookRevision($command, $repository, $transaction)
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

class TestPostCommit extends HookTest
{
    public function setUp()
    {
        $this->hook = new PostCommit_For_Test('tests/tmp/testrepository', 1);
        parent::setUp();
    }

    public function test_PostCommit()
    {
        $request  = $this->hook->getLastRequest();
        $this->assertEquals('usvn.client.hooks.postCommit', $request->getMethod());
        $this->assertSame(array('007', 1, 'toto', 'message de commit', array(array('U', 'tutu'), array('U', 'tata'))), $request->getParams());
    }
}