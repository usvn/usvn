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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

if (is_readable('TestConfiguration.php')) {
    require_once('TestConfiguration.php');
} else {
    require_once('TestConfiguration.php.dist');
}

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Cache_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Europe/Paris'); // to avoid an E_STRICT notice
require_once 'FactoryTest.php';
require_once 'CoreTest.php';
require_once 'FileBackendTest.php';
require_once 'SqliteBackendTest.php';
require_once 'OutputFrontendTest.php';
require_once 'FunctionFrontendTest.php';
require_once 'ClassFrontendTest.php';
require_once 'FileFrontendTest.php';
require_once 'ApcBackendTest.php';
require_once 'MemcachedBackendTest.php';
require_once 'PageFrontendTest.php';
require_once 'ZendPlatformBackendTest.php';

class Zend_Cache_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Cache');
        $suite->addTestSuite('Zend_Cache_FactoryTest');
        $suite->addTestSuite('Zend_Cache_CoreTest');
        $suite->addTestSuite('Zend_Cache_FileBackendTest');
        $suite->addTestSuite('Zend_Cache_OutputFrontendTest');
        $suite->addTestSuite('Zend_Cache_FunctionFrontendTest');
        $suite->addTestSuite('Zend_Cache_ClassFrontendTest');
        $suite->addTestSuite('Zend_Cache_FileFrontendTest');
        $suite->addTestSuite('Zend_Cache_PageFrontendTest');
        if (!defined('TESTS_ZEND_CACHE_SQLITE_ENABLED') || (defined('TESTS_ZEND_CACHE_SQLITE_ENABLED') && TESTS_ZEND_CACHE_SQLITE_ENABLED)) {
            $suite->addTestSuite('Zend_Cache_SqliteBackendTest');
        }
        if (!defined('TESTS_ZEND_CACHE_APC_ENABLED') || (defined('TESTS_ZEND_CACHE_APC_ENABLED') && TESTS_ZEND_CACHE_APC_ENABLED)) {
            $suite->addTestSuite('Zend_Cache_ApcBackendTest');
        }
        if (!defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') || (defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') && TESTS_ZEND_CACHE_MEMCACHED_ENABLED)) {
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_HOST')) { 
                define('TESTS_ZEND_CACHE_MEMCACHED_HOST', '127.0.0.1');
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PORT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PORT', 11211);
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT', true);
            }
            $suite->addTestSuite('Zend_Cache_MemcachedBackendTest');
        }
        if (!defined('TESTS_ZEND_CACHE_PLATFORM_ENABLED') || (defined('TESTS_ZEND_CACHE_PLATFORM_ENABLED') && TESTS_ZEND_CACHE_PLATFORM_ENABLED)) {
            $suite->addTestSuite('Zend_Cache_ZendPlatformBackendTest');
        }
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cache_AllTests::main') {
    Zend_Cache_AllTests::main();
}
