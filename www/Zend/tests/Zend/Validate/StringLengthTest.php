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
 * @version    $Id: StringLengthTest.php 3278 2007-02-07 21:54:50Z darby $
 */


/**
 * @see Zend_Validate_StringLength
 */
require_once 'Zend/Validate/StringLength.php';


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
class Zend_Validate_StringLengthTest extends PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validate_StringLength
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_StringLength object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_StringLength();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        /**
         * The elements of each array are, in order:
         *      - minimum length
         *      - maximum length
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array(0, null, true, array('', 'a', 'ab')),
            array(-1, null, true, array('')),
            array(2, 2, true, array('ab', '  ')),
            array(2, 2, false, array('a', 'abc')),
            array(1, null, false, array('')),
            array(2, 3, true, array('ab', 'abc')),
            array(2, 3, false, array('a', 'abcd'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_StringLength($element[0], $element[1]);
            foreach ($element[3] as $input) {
                $this->assertEquals($element[2], $validator->isValid($input));
            }
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

    /**
     * Ensures that getMin() returns expected default value
     *
     * @return void
     */
    public function testGetMin()
    {
        $this->assertEquals(0, $this->_validator->getMin());
    }

    /**
     * Ensures that getMax() returns expected default value
     *
     * @return void
     */
    public function testGetMax()
    {
        $this->assertEquals(null, $this->_validator->getMax());
    }
}
