<?php
/**
* @package client
* @subpackage install
* @since 0.5
*
* This test use UNIX command svnadmin.
*/

require_once 'USVN/Client/Install.php';
require_once 'USVN/DirectoryUtils.php';

class TestClientInstall extends PHPUnit_Framework_TestCase
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
            new USVN_Client_Install('tests/tmp/fakerepository', 'http://bidon', 'auth007');
        }
        catch (Exception $e)
        {
            return;
        }
        $this->fail();
    }

    public function test_installHook()
    {
        $install = new USVN_Client_Install('tests/tmp/testrepository', 'http://bidon', 'auth007');
        $this->assertTrue(file_exists('tests/tmp/testrepository/hooks/start-commit'));
        $this->assertTrue(file_exists('tests/tmp/testrepository/hooks/pre-commit'));
        $this->assertTrue(is_executable('tests/tmp/testrepository/hooks/pre-commit'), "Hook is not executable");
        $this->assertTrue(file_exists('tests/tmp/testrepository/usvn'));
        $this->assertTrue(file_exists('tests/tmp/testrepository/usvn/USVN/Client/Hooks/StartCommit.php'), "Source code of hooks class is not available.");
        $this->assertTrue(file_exists('tests/tmp/testrepository/usvn/USVN/Client/Config.php'), "Source code of some client class is not available.");
    }

    public function test_configFile()
    {
        $install = new USVN_Client_Install('tests/tmp/testrepository', 'http://bidon', 'auth007');
        $xml = simplexml_load_file('tests/tmp/testrepository/usvn/config.xml');
        $this->assertEquals('http://bidon', (string)$xml->url);
        $this->assertEquals('auth007', (string)$xml->auth);
    }
}