<?php
/**
* @package client
* @subpackage install
*
* This test use UNIX commands rm and svnadmin. It's probably easy to remove
* use of rm but you can't remove use of svnadmin.
*/

require_once 'USVN/Client/Install.php';

class TestClientInstall extends PHPUnit2_Framework_TestCase
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

    public function test_notSvnRepository()
    {
        try
        {
            new USVN_Client_Install('tests/tmp/fakerepository', 'http://bidon', 'user', 'pass');
        }
        catch (Exception $e)
        {
            return;
        }
        $this->fail();
    }

    public function test_installHook()
    {
        $install = new USVN_Client_Install('tests/tmp/testrepository', 'http://bidon', 'user', 'pass');
        $this->assertTrue(file_exists('tests/tmp/testrepository/hooks/start-commit'));
        $this->assertTrue(file_exists('tests/tmp/testrepository/hooks/pre-commit'));
        $this->assertTrue(file_exists('tests/tmp/testrepository/usvn'));    }
}