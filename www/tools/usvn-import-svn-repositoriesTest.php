<?php
/**
 * Import SVN repositories
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7.0
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
    define("PHPUnit_MAIN_METHOD", "importSvnRepositories_Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

define('CONFIG_FILE', 'tests/test.ini');

require_once 'www/USVN/autoload.php';

class importSvnRepositories_Test extends USVN_Test_DB {
	private $_path;

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("importSvnRepositories_Test");
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
		$this->db->closeConnection();
		$config = Zend_Registry::get('config');
		$config->database = array("adapterName" => "mysql");
	}

	public function tearDown()
	{
		chdir($this->_path);
		parent::tearDown();
	}

	public function testNoEnoughArgument()
	{
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php test", $return);
		$this->assertEquals(1, $return, $message);
	}

	public function testBadOptions()
	{
		$path = '../tests/tmp/svn/test/';
		mkdir($path);
		USVN_SVNUtils::createSvn($path.'testSVN');

		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php --test $path", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php --verbosee $path", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php --addmetoagroup $path", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php --noimporte $path", $return);
		$this->assertEquals(1, $return, $message);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php --recurse $path", $return);
		$this->assertEquals(1, $return, $message);

		USVN_DirectoryUtils::removeDirectory($path);
	}

	/*public function testImportOk()
	{
		$path = '../tests/tmp/svn/test/';
		mkdir($path);
		USVN_SVNUtils::createSvn($path.'testSVN');

		$table = new USVN_Db_Table_Users();
		$obj = $table->fetchNew();
		$obj->setFromArray(array('users_login' 			=> 'user_test',
									'users_password' 	=> 'password',
									'users_firstname' 	=> 'firstname',
									'users_lastname' 	=> 'lastname',
									'users_email' 		=> 'email@email.fr'));
		$obj->save();

		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php ../tests/test.ini --addmetogroup --admin --creategroup --verbose user_test $path", $return);
		$this->assertEquals(0, $return, $message);

		USVN_DirectoryUtils::removeDirectory($path);
	}

	public function testRecursiveImportOk()
	{

		$path = '../tests/tmp/svn/recurse/';
		mkdir($path);
		USVN_SVNUtils::createSvn($path.'/test1');
		USVN_SVNUtils::createSvn($path.'/test2');
		mkdir($path.'/dir');
		USVN_SVNUtils::createSvn($path.'/dir/test3');
		USVN_SVNUtils::createSvn($path.'/dir/test4');

		$table = new USVN_Db_Table_Users();
		$obj = $table->fetchNew();
		$obj->setFromArray(array('users_login' 			=> 'user_test',
									'users_password' 	=> 'password',
									'users_firstname' 	=> 'firstname',
									'users_lastname' 	=> 'lastname',
									'users_email' 		=> 'email@email.fr'));
		$obj->save();

		$message = USVN_ConsoleUtils::runCmdCaptureMessage("php tools/usvn-import-svn-repositories.php ../tests/test.ini --addmetogroup --admin --creategroup --recursive user_test $path", $return);
		$this->assertEquals(0, $return, $message);

		USVN_DirectoryUtils::removeDirectory($path);
	}*/
}

// Call importSvnRepositories_Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "importSvnRepositories_Test::main") {
    importSvnRepositories_Test::main();
}
?>
