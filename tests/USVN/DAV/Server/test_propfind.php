<?php
/**
* @package webdav
* @subpackage server
*/

require_once 'USVN/DAV/Server/AbstractPropfindRequestHandler.php';
require_once 'PHPUnit2/Framework/TestCase.php';
require_once 'StubServer.php';

class MyPROPFIND extends AbstractPropfindRequestHandler
{
    public $property_call;

    function __construct($server)
    {
        $this->property = array();
        $this->property['prenom'] = 'julien';
        $this->property['nom'] = 'duponchelle';
        $this->property_call = array();
        $this->property_call['nom'] = false;
        parent::__construct($server);
    }

    protected function getProperty($name)
    {
        $this->property_call[$name] = true;
        return $this->property[$name];
    }
}

class TestPropfind extends PHPUnit2_Framework_TestCase
{
    public function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = 'PROPFIND';
    }

    public function test_simple()
    {
        $server = new StubServer();
        $server->fake_content = '<?xml version="1.0" encoding="utf-8" ?>
            <D:propfind xmlns:D="DAV:">
                <D:prop xmlns:R="http://www.foo.bar/boxschema/">
                    <R:nom/>
                    <R:prenom/>
                </D:prop>
            </D:propfind>
        ';
        $propfind = new MyPROPFIND($server);
        $this->assertEquals($propfind->property_call['nom'], true);
    }
}
