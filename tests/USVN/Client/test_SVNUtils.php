<?php
/**
* @package client
* @subpackage utils
*
* This test use UNIX commands rm and svnadmin. It's probably easy to remove
* use of rm but you can't remove use of svnadmin.
*/

require_once 'USVN/Client/SVNUtils.php';

class TestClientSVNUtils extends PHPUnit2_Framework_TestCase
{
    public function setUp()
    {
        $this->clean();
        exec("svnadmin create tests/tmp/testrepository");
        mkdir('tests/tmp/fakerepository');
    }

    public function tearDown()
    {
        $this->clean();
    }

    private function clean()
    {
        exec("rm -Rf tests/tmp/testrepository");
        @rmdir('tests/tmp/fakerepository');
    }

    public function test_isSVNRepository()
    {
        $this->assertTrue(USVN_Client_SVNUtils::isSVNRepository('tests/tmp/testrepository'));
        $this->assertFalse(USVN_Client_SVNUtils::isSVNRepository('tests/tmp/fakerepository'));
    }

	public function test_changedFiles()
	{
        $this->assertEquals(array(array("U", "tutu")), USVN_Client_SVNUtils::changedFiles("U tutu\n"));
        $this->assertEquals(array(array("U", "tutu"), array("U", "tata")), USVN_Client_SVNUtils::changedFiles("U tutu\nU tata\n"));
        $this->assertEquals(array(array("U", "tutu"), array("U", "U")), USVN_Client_SVNUtils::changedFiles("U tutu\nU U\n"));
        $this->assertEquals(array(array("U", "tutu"), array("U", "hello world"), array("U", "toto")), USVN_Client_SVNUtils::changedFiles("U tutu\nU hello world\nU toto\n"));
	}
}