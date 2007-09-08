<?php
/**
 * @package    Zend_Uri
 * @subpackage UnitTests
 */


/**
 * Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Uri
 * @subpackage UnitTests
 */
class Zend_UriTest extends PHPUnit_Framework_TestCase
{
    public function testSchemeEmpty()
    {
        $this->_testInvalidUri('', '/empty/i');
        $this->_testInvalidUri('://www.zend.com', '/empty/i');
    }

    public function testSchemeUnsupported()
    {
        $this->_testInvalidUri('unsupported', '/unsupported/i');
        $this->_testInvalidUri('unsupported://zend.com', '/unsupported/i');
    }

    public function testSchemeIllegal()
    {
        $this->_testInvalidUri('!@#$%^&*()', '/illegal/i');
    }

    public function testSchemeHttp()
    {
    	$this->_testValidUri('http');
    }

    public function testSchemeHttps()
    {
    	$this->_testValidUri('https');
    }

    public function testSchemeMailto()
    {
        $this->markTestIncomplete('Zend_Uri_Mailto is not implemented yet');
    	$this->_testValidUri('mailto');
    }

    /**
     * Tests that an invalid $uri throws an exception and that the
     * message of that exception matches $regex.
     *
     * @param string $uri
     * @param string $regex
     */
    protected function _testInvalidUri($uri, $regex)
    {
        $e = null;
        try {
            $uri = Zend_Uri::factory($uri);
        } catch (Zend_Uri_Exception $e) {
            $this->assertRegExp($regex, $e->getMessage());
            return;
        }
        $this->fail('Zend_Uri_Exception was expected but not thrown');
    }

    /**
     * Tests that a valid $uri returns a Zend_Uri object.
     *
     * @param string $uri
     */
    protected function _testValidUri($uri)
    {
        $uri = Zend_Uri::factory($uri);
        $this->assertTrue($uri instanceof Zend_Uri, 'Zend_Uri object not returned.');
    }

}
