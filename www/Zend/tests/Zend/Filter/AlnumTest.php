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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AlnumTest.php 5347 2007-06-15 19:30:56Z darby $
 */


/**
 * @see Zend_Filter_Alnum
 */
require_once 'Zend/Filter/Alnum.php';


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_AlnumTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_Alnum object
     *
     * @var Zend_Filter_Alnum
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Alnum object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_Alnum();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            'abc123'  => 'abc123',
            'abc 123' => 'abc123',
            'abcxyz'  => 'abcxyz',
            'AZ@#4.3' => 'AZ43',
            ''        => ''
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testAllowWhiteSpace()
    {
        $this->_filter->allowWhiteSpace = true;

        $valuesExpected = array(
            'abc123'  => 'abc123',
            'abc 123' => 'abc 123',
            'abcxyz'  => 'abcxyz',
            'AZ@#4.3' => 'AZ43',
            ''        => '',
            "\n"      => "\n",
            " \t "    => " \t "
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }
}
