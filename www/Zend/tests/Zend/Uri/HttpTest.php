<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: HttpTest.php 5051 2007-05-29 19:31:09Z darby $
 */


/**
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Uri_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests for proper URI decomposition
     */
    public function testSimple()
    {
        $this->_testValidUri('http://www.zend.com');
    }

    public function testAllParts()
    {
        $this->_testValidUri('http://andi:password@www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testUsernamePortPathQueryFragment()
    {
        $this->_testValidUri('http://andi@www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testPortPathQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testPathQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com/path/to/file?a=1&b=2#top');
    }

    public function testQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com/?a=1&b=2#top');
    }

    public function testFragment()
    {
        $this->_testValidUri('http://www.zend.com/#top');
    }

    public function testUsernamePassword()
    {
        $this->_testValidUri('http://andi:password@www.zend.com');
    }

    public function testUsernamePasswordColon()
    {
        $this->_testValidUri('http://an:di:password@www.zend.com');
    }

    public function testUsernamePasswordValidCharacters()
    {
        $this->_testValidUri('http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com');
    }

    public function testUsernameInvalidCharacter()
    {
        $this->_testInvalidUri('http://an`di:password@www.zend.com');
    }

    public function testNoUsernamePassword()
    {
        $this->_testInvalidUri('http://:password@www.zend.com');
    }

    public function testPasswordInvalidCharacter()
    {
        $this->_testInvalidUri('http://andi:pass%word@www.zend.com');
    }

    public function testHostAsIP()
    {
        $this->_testValidUri('http://127.0.0.1');
    }

    public function testLocalhost()
    {
        $this->_testValidUri('http://localhost');
    }

    public function testLocalhostLocaldomain()
    {
        $this->_testValidUri('http://localhost.localdomain');
    }

    public function testSquareBrackets()
    {
        $this->_testValidUri('https://example.com/foo/?var[]=1&var[]=2&some[thing]=3');
    }

    /**
     * Ensures that successive slashes are considered valid
     *
     * @return void
     */
    public function testSuccessiveSlashes()
    {
        $this->_testValidUri('http://example.com//');
        $this->_testValidUri('http://example.com///');
        $this->_testValidUri('http://example.com/foo//');
        $this->_testValidUri('http://example.com/foo///');
        $this->_testValidUri('http://example.com/foo//bar');
        $this->_testValidUri('http://example.com/foo///bar');
        $this->_testValidUri('http://example.com/foo//bar/baz//fob/');
    }

    /**
     * Test a known valid URI
     *
     * @param string $uri
     */
    protected function _testValidUri($uri)
    {
        $obj = Zend_Uri::factory($uri);
        $this->assertEquals($uri, $obj->getUri(), 'getUri() returned value that differs from input');
    }

    /**
     * Test a known invalid URI
     *
     * @param string $uri
     */
    protected function _testInvalidUri($uri)
    {
        try {
            $obj = Zend_Uri::factory($uri);
            $this->fail('Zend_Uri_Exception was expected but not thrown');
        } catch (Zend_Uri_Exception $e) {
        }
    }
}
