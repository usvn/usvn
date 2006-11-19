<?php
require_once 'USVN/DAV/Server/AbstractPropfindRequestHandler.php';
require_once 'PHPUnit2/Framework/TestCase.php';
require_once 'StubServer.php';

class MyPROPFIND extends AbstractPropfindRequestHandler
{
    public $property_call;

    protected $properties = array('prenom', 'nom', 'dog');

    function __construct($server)
    {
        $this->property = array();
        $this->property['prenom'] = 'julien';
        $this->property['nom'] = 'duponchelle';
        $this->property['dog'] = 'tolstoi';
        $this->property_call = array();
        $this->property_call['nom'] = false;
        $this->property_call['prenom'] = false;
        $this->property_call['dog'] = false;
        parent::__construct($server);
    }

    protected function getProperty($name)
    {
        $this->property_call[$name] = true;
        return $this->property[$name];
    }
}

/**
* @package webdav
* @subpackage server
*/
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
        $this->assertEquals($propfind->property_call['prenom'], true);
        $this->assertEquals($propfind->property_call['dog'], false);
    }

    public function test_allprop()
    {
        $server = new StubServer();
        $server->fake_content = '<?xml version="1.0" encoding="utf-8" ?>
            <D:propfind xmlns:D="DAV:">
                <D:allprop/>
            </D:propfind>
        ';
        $propfind = new MyPROPFIND($server);
        $this->assertEquals($propfind->property_call['nom'], true);
        $this->assertEquals($propfind->property_call['prenom'], true);
        $this->assertEquals($propfind->property_call['dog'], true);
    }

    public function test_getResponse()
    {
        $server = new StubServer();
        $server->fake_content = '<?xml version="1.0" encoding="utf-8" ?>
            <D:propfind xmlns:D="DAV:">
                <D:allprop/>
            </D:propfind>
        ';
        $propfind = new MyPROPFIND($server);
        $this->assertEquals($propfind->getResponse(), '<?xml version="1.0" encoding="utf-8" ?><D:multistatus xmlns:D="DAV:"><D:response><prenom>julien</prenom><nom>duponchelle</nom><dog>tolstoi</dog> </D:response></D:multistatus>');
    }
}
