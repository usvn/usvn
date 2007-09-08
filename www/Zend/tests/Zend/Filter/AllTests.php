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
 * @version    $Id: AllTests.php 5127 2007-06-05 20:26:59Z andries $
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_AllTests::main');
}


/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';


/**
 * PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';


/**
 * PHPUnit_TextUI_TestRunner
 */
require_once 'PHPUnit/TextUI/TestRunner.php';


/**
 * @see Zend_Filter_AlnumTest
 */
require_once 'Zend/Filter/AlnumTest.php';


/**
 * @see Zend_Filter_AlphaTest
 */
require_once 'Zend/Filter/AlphaTest.php';


/**
 * @see Zend_Filter_BaseNameTest
 */
require_once 'Zend/Filter/BaseNameTest.php';


/**
 * @see Zend_Filter_DigitsTest
 */
require_once 'Zend/Filter/DigitsTest.php';


/**
 * @see Zend_Filter_DirTest
 */
require_once 'Zend/Filter/DirTest.php';


/**
 * @see Zend_Filter_HtmlEntitiesTest
 */
require_once 'Zend/Filter/HtmlEntitiesTest.php';


/**
 * @see Zend_Filter_IntTest
 */
require_once 'Zend/Filter/IntTest.php';


/**
 * @see Zend_Filter_RealPathTest
 */
require_once 'Zend/Filter/RealPathTest.php';


/**
 * @see Zend_Filter_StringToLowerTest
 */
require_once 'Zend/Filter/StringToLowerTest.php';

/**
 * @see Zend_Filter_StringToUpperTest
 */
require_once 'Zend/Filter/StringToUpperTest.php';


/**
 * @see Zend_Filter_StringTrimTest
 */
require_once 'Zend/Filter/StringTrimTest.php';


/**
 * @see Zend_Filter_StripTagsTest
 */
require_once 'Zend/Filter/StripTagsTest.php';


/**
 * @see Zend_Filter_InputTest
 */
require_once 'Zend/Filter/InputTest.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Filter');

        $suite->addTestSuite('Zend_Filter_AlnumTest');
        $suite->addTestSuite('Zend_Filter_AlphaTest');
        $suite->addTestSuite('Zend_Filter_BaseNameTest');
        $suite->addTestSuite('Zend_Filter_DigitsTest');
        $suite->addTestSuite('Zend_Filter_DirTest');
        $suite->addTestSuite('Zend_Filter_HtmlEntitiesTest');
        $suite->addTestSuite('Zend_Filter_IntTest');
        $suite->addTestSuite('Zend_Filter_RealPathTest');
        $suite->addTestSuite('Zend_Filter_StringToLowerTest');
        $suite->addTestSuite('Zend_Filter_StringToUpperTest');
        $suite->addTestSuite('Zend_Filter_StringTrimTest');
        $suite->addTestSuite('Zend_Filter_StripTagsTest');
        $suite->addTestSuite('Zend_Filter_InputTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_AllTests::main') {
    Zend_Filter_AllTests::main();
}
