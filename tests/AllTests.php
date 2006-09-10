<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
 
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

$path = '../';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'test_fichier_bidon_pour_test.php';

class Framework_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');
 
        $suite->addTestSuite('TestBidon');
 
        return $suite;
    }
}

 
class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
 
        $suite->addTest(Framework_AllTests::suite());
        // ...
 
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
?>