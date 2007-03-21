<?php
// Call USVN_DirectoryUtilsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_DirectoryUtilsTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

/**
 * Test class for USVN_DirectoryUtils.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-10 at 16:12:51.
 */
class USVN_DirectoryUtilsTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_DirectoryUtilsTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function test_removeDirectory()
	{
		@mkdir('tests/tmp/dir');
		@mkdir('tests/tmp/dir/1');
		@mkdir('tests/tmp/dir/2');
		@mkdir('tests/tmp/dir/2/3');
		file_put_contents('tests/tmp/dir/2/3/test', 'tutu');
		file_put_contents('tests/tmp/dir/test', 'tutu');
		USVN_DirectoryUtils::removeDirectory('tests/tmp/dir');
		$this->assertFalse(file_exists('tests/tmp/dir'));
	}
}

// Call USVN_DirectoryUtilsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_DirectoryUtilsTest::main") {
    USVN_DirectoryUtilsTest::main();
}
?>