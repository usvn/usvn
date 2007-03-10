<?php
/**
* @package client
* @subpackage uninstall
* @since 0.5
*
* This test use UNIX command svnadmin.
*/

require_once 'USVN/Client/Install.php';
require_once 'USVN/Client/Uninstall.php';
require_once 'USVN/DirectoryUtils.php';

class TestClientUninstall extends PHPUnit_Framework_TestCase
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
        USVN_DirectoryUtils::removeDirectory("tests/tmp/testrepository");
        USVN_DirectoryUtils::removeDirectory('tests/tmp/fakerepository');
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
        $this->assertFalse(file_exists('tests/tmp/testrepository/usvn'));
    }
}