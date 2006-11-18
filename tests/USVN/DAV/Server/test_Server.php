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
        $this->assertEquals($server->getRequestMethod(), 'MKACTIVITY');
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $server = new Server();
        $this->assertEquals($server->getRequestMethod(), 'GET');
    }

    public function test_getContent()
    {
        $server = new Server();
        $this->assertEquals($server->getContent(), '');

        $f = fopen('test.tmp', 'w+');
        fputs($f, 'Youpi');
        fclose($f);
        $server = new Server('test.tmp');
        $this->assertEquals($server->getContent(), 'Youpi');
    }
}
