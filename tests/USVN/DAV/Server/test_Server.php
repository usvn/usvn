<?php
/**
* @package webdav
* @subpackage server
*/

require_once 'PHPUnit2/Framework/TestCase.php';
require_once 'USVN/DAV/Server/Server.php';

class TestDavServer extends PHPUnit2_Framework_TestCase
{
    public function test_getMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'MKACTIVITY';
        $server = new Server();
        $this->assertEquals('MKACTIVITY', $server->getRequestMethod());
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $server = new Server();
        $this->assertEquals('GET', $server->getRequestMethod());
    }

    public function test_getUrl()
    {
        $_SERVER['SCRIPT_NAME'] = '/test/toto';
        $server = new Server();
        $this->assertEquals('/test/toto', $server->getRequestUrl());
    }

    public function test_getRequestContent()
    {
        $server = new Server();
        $this->assertEquals($server->getRequestContent(), '');

        $f = fopen('tests/tmp/test.tmp', 'w+');
        fputs($f, 'Youpi');
        fclose($f);
        $server = new Server('tests/tmp/test.tmp');
        $this->assertEquals('Youpi', $server->getRequestContent());
    }
}
