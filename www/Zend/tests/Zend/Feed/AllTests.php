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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 3941 2007-03-14 21:36:13Z darby $
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Feed_AllTests::main');

    /**
     * Prepend library/ to the include_path.  This allows the tests to run out of the box and
     * helps prevent finding other copies of the framework that might be present.
     */
    set_include_path(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library'
                     . PATH_SEPARATOR . get_include_path());
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Feed/ArrayAccessTest.php';
require_once 'Zend/Feed/AtomEntryOnlyTest.php';
require_once 'Zend/Feed/AtomPublishingTest.php';
require_once 'Zend/Feed/CountTest.php';
require_once 'Zend/Feed/ElementTest.php';
require_once 'Zend/Feed/ImportTest.php';
require_once 'Zend/Feed/IteratorTest.php';


/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Feed');

        $suite->addTestSuite('Zend_Feed_ArrayAccessTest');
        $suite->addTestSuite('Zend_Feed_AtomEntryOnlyTest');
        $suite->addTestSuite('Zend_Feed_AtomPublishingTest');
        $suite->addTestSuite('Zend_Feed_CountTest');
        $suite->addTestSuite('Zend_Feed_ElementTest');
        $suite->addTestSuite('Zend_Feed_ImportTest');
        $suite->addTestSuite('Zend_Feed_IteratorTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Feed_AllTests::main') {
    Zend_Feed_AllTests::main();
}
