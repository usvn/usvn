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
 * @version    $Id: AlphaTest.php 5470 2007-06-28 17:05:56Z andries $
 */


/**
 * @see Zend_Filter_Alpha
 */
require_once 'Zend/Filter/Alpha.php';


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
class Zend_Filter_AlphaTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_Alpha object
     *
     * @var Zend_Filter_Alpha
     */
    protected $_filter;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     *
     * @var mixed
     **/
    protected static $_unicodeEnabled;

    /**
     * Creates a new Zend_Filter_Alpha object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_Alpha();
        if (null === self::$_unicodeEnabled) {
            self::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        if (!self::$_unicodeEnabled) {
            // Sorry folks, no unicode tests for you
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc',
                'abcxyz'        => 'abcxyz',
                ''              => ''
                );
        } else {
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc',
                'abcxyz'        => 'abcxyz',
                'četně'         => 'četně',
                'لعربية'        => 'لعربية',
                'grzegżółka'    => 'grzegżółka',
                'België'        => 'België',
                ''              => ''
                );
        }

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testAllowWhiteSpace()
    {
        $this->_filter->allowWhiteSpace = true;
        if (!self::$_unicodeEnabled) {
            // Sorry folks, no unicode tests for you
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc ',
                'abcxyz'        => 'abcxyz',
                ''              => '',
                "\n"            => "\n",
                " \t "          => " \t "
                );
        } else {
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc ',
                'abcxyz'        => 'abcxyz',
                'četně'         => 'četně',
                'لعربية'        => 'لعربية',
                'grzegżółka'    => 'grzegżółka',
                'België'        => 'België',
                ''              => '',
                "\n"            => "\n",
                " \t "          => " \t "
                );
        }

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }
}
