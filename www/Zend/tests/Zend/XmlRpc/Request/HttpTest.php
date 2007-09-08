<?php
require_once 'Zend/XmlRpc/Request/Http.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test case for Zend_XmlRpc_Request_Http
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id: HttpTest.php 3833 2007-03-09 05:01:14Z matthew $
 */
class Zend_XmlRpc_Request_HttpTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->xml =<<<EOX
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>test.userUpdate</methodName>
    <params>
        <param>
            <value><string>blahblahblah</string></value>
        </param>
        <param>
            <value><struct>
                <member>
                    <name>salutation</name>
                    <value><string>Felsenblöcke</string></value>
                </member>
                <member>
                    <name>firstname</name>
                    <value><string>Lépiné</string></value>
                </member>
                <member>
                    <name>lastname</name>
                    <value><string>Géranté</string></value>
                </member>
                <member>
                    <name>company</name>
                    <value><string>Zend Technologies, Inc.</string></value>
                </member>
            </struct></value>
        </param>
    </params>
</methodCall>
EOX;
        $this->request = new Zend_XmlRpc_Request_Http();
        $this->request->loadXml($this->xml);

        $this->server = $_SERVER;
        foreach ($_SERVER as $key => $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                unset($_SERVER[$key]);
            }
        }
        $_SERVER['HTTP_USER_AGENT']     = 'Zend_XmlRpc_Client';
        $_SERVER['HTTP_HOST']           = 'localhost';
        $_SERVER['HTTP_CONTENT_TYPE']   = 'text/xml';
        $_SERVER['HTTP_CONTENT_LENGTH'] = 958;
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        $_SERVER = $this->server;
        unset($this->request);
    }

    public function testGetRawRequest()
    {
        $this->assertEquals($this->xml, $this->request->getRawRequest());
    }

    public function testGetHeaders()
    {
        $expected = array(
            'User-Agent'     => 'Zend_XmlRpc_Client',
            'Host'           => 'localhost',
            'Content-Type'   => 'text/xml',
            'Content-Length' => 958
        );
        $this->assertEquals($expected, $this->request->getHeaders());
    }

    public function testGetFullRequest()
    {
        $expected =<<<EOT
User-Agent: Zend_XmlRpc_Client
Host: localhost
Content-Type: text/xml
Content-Length: 958

EOT;
        $expected .= $this->xml;

        $this->assertEquals($expected, $this->request->getFullRequest());
    }
}
