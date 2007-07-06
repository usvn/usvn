<?php
/**
 * Command line installer for USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6.3
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "InstallCommandLine_Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';


class InstallCommandLine_Test extends PHPUnit_Framework_TestCase {
	private $_path;

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("InstallCommandLine_Test");
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
		chdir($this->_path);
		chdir('www');
		USVN_Translation::initTranslation('en_US', 'locale');
		USVN_DirectoryUtils::removeDirectory('../tests/');
		mkdir("../tests");
		chmod("install/install-commandline.php", 0700);
	}

	public function tearDown()
	{
		chdir($this->_path);
	}

	public function testNoEnoughArgument()
	{
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("install/install-commandline.php", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("install/install-commandline.php test", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("install/install-commandline.php test test", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("install/install-commandline.php test test test test test", $return);
		$this->assertEquals(1, $return, $message);
	}

	public function testbadConfigFile()
	{
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("install/install-commandline.php fake.ini test test test", $return);
		$this->assertEquals(1, $return, $message);
	}


	public function testinstallSqlite()
	{
		file_put_contents('../tests/config.ini', '[general]
url.base = "/usvn"
translation.locale = "en_US"
template.name = "default"
site.title = "USVN"
site.ico = "medias/default/images/USVN.ico"
site.logo = "medias/default/images/USVN-logo.png"
subversion.path = "../tests/"
subversion.passwd = "../tests/htpasswd"
subversion.authz = "../tests/authz"
subversion.url = "http://localhost/usvn/svn/"
database.adapterName = "PDO_SQLITE"
database.prefix = "usvn_"
database.options.dbname = "../tests/usvn.db"
		');
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("install/install-commandline.php ../tests/config.ini ../tests/htaccess admin testtest", $return);
		$this->assertEquals(0, $return, $message);
		echo $message;
		$config = new USVN_Config_Ini('../tests/config.ini', 'general');
		$this->assertTrue(isset($config->version));
		$this->assertTrue(file_exists("../tests/htaccess"), "htpasswd not create");
		$this->assertTrue(file_exists("../tests/htpasswd"), "htpasswd not create");
		$this->assertTrue(file_exists("../tests/authz"), "authz not create");
	}

}

// Call InstallCommandLine_Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "InstallCommandLine_Test::main") {
    InstallCommandLine_Test::main();
}
?>
