<?php
/**
 * Test USVN client hooks
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call USVN_HooksTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_HooksTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once "www/USVN/autoload.php";
require_once "client/clientTest.php";

class USVN_HooksTest extends Abstract_USVN_ClientTest {
	private $config;
	private $base_url;
	private $old_db_user;
	private $old_db_password;
	private $old_db_database;


	public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_HooksTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function setUp()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			if (!file_exists('USVN')) {
				exec("ln -s www/USVN");
			}
		}
		$this->config = new USVN_Config("www/USVN/config.ini", "general");
		$this->old_db_database = $this->config->database->options->dbname;
		$this->config->database->options->dbname = "usvn-test";
		$this->old_db_password = $this->config->database->options->password;
		$this->config->database->options->password = "usvn-test";
		$this->old_db_user = $this->config->database->options->username;
		$this->config->database->options->username = "usvn-test";
		$this->base_url = $this->config->url->base;
		$this->config->save();
		parent::setUp();
	}

	public function tearDown()
	{
		$this->config->database->options->dbname = $this->old_db_database ;
		$this->config->database->options->password = $this->old_db_password;
		$this->config->database->options->username = $this->old_db_user;
		$this->config->save();
		parent::tearDown();
	}

	public function testHooksStartCommit()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			system("cd tests/testclient/test && ./hooks/start-commit " . $this->repository_path . " noplay", $return);
			$this->assertEquals(0, $return);
		}
	}

	public function testHooksPostCommit()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			system("cd tests/testclient/test && ./hooks/post-commit " . $this->repository_path . " 1", $return);
			$this->assertEquals(0, $return);
		}
	}

	public function testHooksPostLock()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			$p = popen("cd tests/testclient/test && ./hooks/post-lock " . $this->repository_path . " noplay", "w");
			fwrite($p, "fichier1");
			$return = pclose($p);
			$this->assertEquals(0, $return);
		}
	}

	public function testHooksPreLock()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			system("cd tests/testclient/test && ./hooks/pre-lock " . $this->repository_path . " fichier2 noplay", $return);
			$this->assertEquals(0, $return);
		}
	}

	public function testHooksPreUnlock()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			system("cd tests/testclient/test && ./hooks/pre-unlock " . $this->repository_path . " fichier2 noplay", $return);
			$this->assertEquals(0, $return);
		}
	}

	public function testHooksPostUnlock()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			$p = popen("cd tests/testclient/test && ./hooks/post-unlock " . $this->repository_path . " noplay", "w");
			fwrite($p, "fichier1");
			$return = pclose($p);
			$this->assertEquals(0, $return);
		}
	}

	public function testPreRevpropChange()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			$p = popen("cd tests/testclient/test && ./hooks/pre-revprop-change " . $this->repository_path . " 1 noplay svn:log M", "w");
			fwrite($p, "Test");
			$return = pclose($p);
			$this->assertEquals(0, $return);
		}
	}

	public function testPostRevpropChange()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			$p = popen("cd tests/testclient/test && ./hooks/post-revprop-change " . $this->repository_path . " 1 noplay svn:log M", "w");
			fwrite($p, "Test");
			$return = pclose($p);
			$this->assertEquals(0, $return);
		}
	}
}

// Call USVN_HooksTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_HooksTest::main") {
    USVN_HooksTest::main();
}
?>
