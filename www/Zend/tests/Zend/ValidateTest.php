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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: ValidateTest.php 4974 2007-05-25 21:11:56Z bkarwin $
 */


/**
 * @see Zend_Validate
 */
require_once 'Zend/Validate.php';

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_ValidateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate object
     *
     * @var Zend_Validate
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate();
    }

    /**
     * Ensures expected results from empty validator chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertEquals(array(), $this->_validator->getErrors());
        $this->assertTrue($this->_validator->isValid('something'));
        $this->assertEquals(array(), $this->_validator->getErrors());
    }

    /**
     * Ensures expected behavior from a validator known to succeed
     *
     * @return void
     */
    public function testTrue()
    {
        $this->_validator->addValidator(new Zend_ValidateTest_True());
        $this->assertTrue($this->_validator->isValid(null));
        $this->assertEquals(array(), $this->_validator->getMessages());
        $this->assertEquals(array(), $this->_validator->getErrors());
    }

    /**
     * Ensures expected behavior from a validator known to fail
     *
     * @return void
     */
    public function testFalse()
    {
        $this->_validator->addValidator(new Zend_ValidateTest_False());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('validation failed'), $this->_validator->getMessages());
        $this->assertEquals(array('error'), $this->_validator->getErrors());
    }

    /**
     * Ensures that a validator may break the chain
     *
     * @return void
     */
    public function testBreakChainOnFailure()
    {
        $this->_validator->addValidator(new Zend_ValidateTest_False(), true)
                         ->addValidator(new Zend_ValidateTest_False());
        $this->assertFalse($this->_validator->isValid(null));
        $this->assertEquals(array('validation failed'), $this->_validator->getMessages());
        $this->assertEquals(array('error'), $this->_validator->getErrors());
    }

    /**
     * Ensures that we can call the static method is()
     * to instantiate a named validator by its class basename
     * and it returns the result of isValid() with the input.
     */
    public function testStaticFactory()
    {
        $this->assertTrue(Zend_Validate::is('1234', 'Digits'));
        $this->assertFalse(Zend_Validate::is('abc', 'Digits'));
    }

    /**
     * Ensures that a validator with constructor arguments can be called
     * with the static method is().
     */
    public function testStaticFactoryWithConstructorArguments()
    {
        $this->assertTrue(Zend_Validate::is('12', 'Between', array(1, 12)));
        $this->assertFalse(Zend_Validate::is('24', 'Between', array(1, 12)));
    }

    /**
     * Ensures that if we specify a validator class basename that doesn't
     * exist in the namespace, is() throws an exception.
     */
    public function testStaticFactoryClassNotFound()
    {
        try {
            $this->assertTrue(Zend_Validate::is('1234', 'UnknownValidator'));
            $this->fail('Expected to catch Zend_Validate_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Validate_Exception', $e,
                'Expected exception of type Zend_Validate_Exception, got '.get_class($e));
            $this->assertEquals("Validate class not found from basename 'UnknownValidator'", $e->getMessage());
        }
    }

}


/**
 * Validator to return true to any input.
 */
class Zend_ValidateTest_True extends Zend_Validate_Abstract
{
    protected $_messages = array();
    protected $_errors = array();

    public function isValid($value)
    {
        return true;
    }

}


/**
 * Validator to return false to any input.
 */
class Zend_ValidateTest_False extends Zend_Validate_Abstract
{
    protected $_messages = array();
    protected $_errors = array();

    public function isValid($value)
    {
        $this->_messages = array('validation failed');
        $this->_errors = array('error');
        return false;
    }

    public function getMessages()
    {
        return $this->_messages;
    }

}
