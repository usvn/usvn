<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Http/ResponseTest.php';
require_once 'Zend/Http/CookieTest.php';
require_once 'Zend/Http/CookieJarTest.php';
require_once 'Zend/Http/Client/AllTests.php';

class Zend_Http_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTestSuite('Zend_Http_ResponseTest');
        $suite->addTestSuite('Zend_Http_CookieTest');
        $suite->addTestSuite('Zend_Http_CookieJarTest');
        $suite->addTest(Zend_Http_Client_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_AllTests::main') {
    Zend_AllTests::main();
}
