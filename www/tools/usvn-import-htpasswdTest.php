<?php
/**
 * Command line tools for import password into USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6.4
 * @package tools
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "importHtpasswdCommandLine_Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';


class importHtpasswdCommandLine_Test extends USVN_Test_DB {
	private $_path;

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("importHtpasswdCommandLine_Test");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function __construct()
	{
		parent::__construct();
		$this->_path = getcwd();
	}

	public function __delete()
	{
		chdir($this->_path);
		parent::__delete();
	}

	public function setUp()
	{
		parent::setUp();
		chdir($this->_path);
		chdir('www');
		USVN_Translation::initTranslation('en_US', 'locale');
		chmod("tools/usvn-import-htpasswd.php", 0700);
	}

	public function tearDown()
	{
		chdir($this->_path);
		parent::tearDown();
	}

	public function testNoEnoughArgument()
	{
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-htpasswd.php", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-htpasswd.php test", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-htpasswd.php test test test", $return);
		$this->assertEquals(1, $return, $message);
	}

	public function testbadHtpasswdFile()
	{
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-htpasswd.php ../tests/test.ini ../tests/htpasswd", $return);
		$this->assertEquals(1, $return, $message);
	}

	public function testbadConfigFile()
	{
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-htpasswd.php fake.ini ../tests/fake", $return);
		$this->assertEquals(1, $return, $message);
	}

	public function testImportHtpasswd()
	{
		file_put_contents('../tests/htpasswd', "noplay:BD3ZmTBhHmWJs\nstem:1YApoa5EK/WFs");
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-htpasswd.php ../tests/test.ini ../tests/htpasswd", $return);
		$this->assertEquals(0, $return, $message);
		echo $message;
		chdir($this->_path); //Else SQLite doesn't work
		$userTable = new USVN_Db_Table_Users();
		$user = $userTable->fetchRow(array('users_login = ?' => "noplay"));
		$this->assertNotNull($user);
		$this->assertEquals("BD3ZmTBhHmWJs", $user->password);
	}
}

// Call importHtpasswdCommandLine_Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "importHtpasswdCommandLine_Test::main") {
    importHtpasswdCommandLine_Test::main();
}
?>
