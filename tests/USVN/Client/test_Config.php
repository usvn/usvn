<?php
/**
* @package client
* @subpackage config
* @since 0.5
*/

require_once 'USVN/Client/Config.php';
require_once 'USVN/DirectoryUtils.php';

class TestClientConfig extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->clean();
        mkdir('tests/tmp/testrepository');
        mkdir('tests/tmp/testrepository/usvn');
    }

    public function tearDown()
    {
        $this->clean();
    }

    private function clean()
    {
        USVN_DirectoryUtils::removeDirectory("tests/tmp/testrepository");
    }

    public function test_creationFichier()
    {
        $config = new USVN_Client_Config('tests/tmp/testrepository');
        $config->save();
        $this->assertTrue(file_exists('tests/tmp/testrepository/usvn/config.xml'));
    }

    public function test_chargementFichier()
    {
        $xml = new SimpleXMLElement("<usvn><url>http://www.usvn.fr</url></usvn>");
        file_put_contents('tests/tmp/testrepository/usvn/config.xml', $xml->asXml());

        $config = new USVN_Client_Config('tests/tmp/testrepository');
        $this->assertEquals('http://www.usvn.fr', $config->url);
    }

    public function test_creationVariable()
    {
        $config = new USVN_Client_Config('tests/tmp/testrepository');
        $config->url = 'http://www.usvn.fr';
        $config->save();

        $config2 = new USVN_Client_Config('tests/tmp/testrepository');
        $this->assertEquals('http://www.usvn.fr', $config2->url);
    }
}