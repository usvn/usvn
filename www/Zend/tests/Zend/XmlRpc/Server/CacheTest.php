<?php
require_once 'Zend/XmlRpc/Server.php';
require_once 'Zend/XmlRpc/Server/Cache.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

/**
 * Test case for Zend_XmlRpc_Server_Cache
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id: CacheTest.php 2332 2006-12-14 18:11:13Z matthew $
 */
class Zend_XmlRpc_Server_CacheTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_XmlRpc_Server object
     * @var Zend_XmlRpc_Server
     */
    protected $_server;

    /**
     * Local file for caching
     * @var string 
     */
    protected $_file;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_file = realpath(dirname(__FILE__)) . '/xmlrpc.cache';
        $this->_server = new Zend_XmlRpc_Server();
        $this->_server->setClass('Zend_XmlRpc_Server_Cache', 'cache');
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        if (file_exists($this->_file)) {
            unlink($this->_file);
        }
        unset($this->_server);
    }

    /**
     * Tests functionality of both get() and save()
     */
    public function testGetSave()
    {
        if (!is_writeable('./')) {
            throw new PHPUnit_Framework_IncompleteTestError('Directory not writeable');
        }

        $this->assertTrue(Zend_XmlRpc_Server_Cache::save($this->_file, $this->_server));
        $expected = $this->_server->listMethods();
        $server = new Zend_XmlRpc_Server();
        $this->assertTrue(Zend_XmlRpc_Server_Cache::get($this->_file, $server));
        $actual = $server->listMethods();

        $this->assertSame($expected, $actual);
    }

    /**
     * Zend_XmlRpc_Server_Cache::delete() test
     */
    public function testDelete()
    {
        if (!is_writeable('./')) {
            throw new PHPUnit_Framework_IncompleteTestError('Directory not writeable');
        }

        $this->assertTrue(Zend_XmlRpc_Server_Cache::save($this->_file, $this->_server));
        $this->assertTrue(Zend_XmlRpc_Server_Cache::delete($this->_file));
    }
}
