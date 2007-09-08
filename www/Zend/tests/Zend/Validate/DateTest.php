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
 * @version    $Id: DateTest.php 3376 2007-02-13 19:49:11Z darby $
 */


/**
 * @see Zend_Validate_Date
 */
require_once 'Zend/Validate/Date.php';


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
class Zend_Validate_DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_Date object
     *
     * @var Zend_Validate_Date
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Date object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_Date();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            '2007-01-01' => true,
            '2007-02-28' => true,
            '2007-02-29' => false,
            '2008-02-29' => true,
            '2007-02-30' => false,
            '2007-02-99' => false,
            '9999-99-99' => false,
            0            => false,
            999999999999 => false,
            'Jan 1 2007' => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input),
                                "'$input' expected to be " . ($result ? '' : 'in') . 'valid');
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }
}
