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
        USVN_DirectoryUtils::removeDirectory("tests/tmp/testrepository");
        USVN_DirectoryUtils::removeDirectory('tests/tmp/fakerepository');
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
        $this->assertTrue(file_exists('tests/tmp/testrepository/usvn'));
    }

    public function test_configFile()
    {
        $install = new USVN_Client_Install('tests/tmp/testrepository', 'http://bidon', 'user', 'pass');
        $xml = simplexml_load_file('tests/tmp/testrepository/usvn/config.xml');
        $this->assertEquals('http://bidon', (string)$xml->url);
        $this->assertEquals('user', (string)$xml->user);
        $this->assertEquals('pass', (string)$xml->password);
    }
}