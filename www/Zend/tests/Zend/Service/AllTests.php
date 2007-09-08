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
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 5115 2007-06-05 00:11:42Z bkarwin $
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_AllTests::main');
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
 * @see Zend_Service_AkismetTest
 */
require_once 'Zend/Service/AkismetTest.php';


/**
 * @see Zend_Service_Amazon_AllTests
 */
require_once 'Zend/Service/Amazon/AllTests.php';


/**
 * @see Zend_Service_Audioscrobbler_AllTests
 */
require_once 'Zend/Service/Audioscrobbler/AllTests.php';


/**
 * @see Zend_Service_Delicious_AllTests
 */
require_once 'Zend/Service/Delicious/AllTests.php';


/**
 * @see Zend_Service_Flickr_AllTests
 */
require_once 'Zend/Service/Flickr/AllTests.php';


/**
 * @see Zend_Service_SimpyTest
 */
require_once 'Zend/Service/SimpyTest.php';

/**
 * @see Zend_Service_StrikeIron_AllTests
 */
require_once 'Zend/Service/StrikeIron/AllTests.php';


/**
 * @see Zend_Service_Yahoo_AllTests
 */
require_once 'Zend/Service/Yahoo/AllTests.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service');

        $suite->addTestSuite('Zend_Service_AkismetTest');
        $suite->addTest(Zend_Service_Amazon_AllTests::suite());
        $suite->addTest(Zend_Service_Audioscrobbler_AllTests::suite());
        $suite->addTest(Zend_Service_Delicious_AllTests::suite());
        $suite->addTest(Zend_Service_Flickr_AllTests::suite());
        if (defined('TESTS_ZEND_SERVICE_SIMPY_ENABLED') && constant('TESTS_ZEND_SERVICE_SIMPY_ENABLED')) {
            $suite->addTestSuite('Zend_Service_SimpyTest');
        } else {
            $suite->addTestSuite('Zend_Service_SimpyTest_Skip');
        }
        $suite->addTest(Zend_Service_StrikeIron_AllTests::suite());
        $suite->addTest(Zend_Service_Yahoo_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_AllTests::main') {
    Zend_Service_AllTests::main();
}
