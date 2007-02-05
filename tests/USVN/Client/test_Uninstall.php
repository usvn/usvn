<?php
/**
* @package client
* @subpackage uninstall
*
* This test use UNIX commands rm and svnadmin. It's probably easy to remove
* use of rm but you can't remove use of svnadmin.
*/

require_once 'USVN/Client/Install.php';
require_once 'USVN/Client/Uninstall.php';

class TestClientUninstall extends PHPUnit2_Framework_TestCase
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
            new USVN_Client_Uninstall('tests/tmp/fakerepository');
        }
        catch (Exception $e)
        {
            return;
        }
        $this->fail();
    }

    public function test_uninstallHook()
    {
        $install = new USVN_Client_Install('tests/tmp/testrepository', 'http://bidon', 'user', 'pass');
        $unistall = new USVN_Client_Uninstall('tests/tmp/testrepository');
        $this->assertFalse(file_exists('tests/tmp/testrepository/hooks/start-commit'));
    }
}