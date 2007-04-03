<?php
/**
 * Base class for test USVN client
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

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once "www/USVN/autoload.php";

abstract class Abstract_USVN_ClientTest extends USVN_Test_DB {
	private $config;
	private $base_url;
	private $old_db_user;
	private $old_db_password;
	private $old_db_database;
	protected $hooks_url;
	protected $repository_path;

	/**
	* Run a command and return the result
	* Console output is hide
	*
	* @param string
	* @param int Return code of the command
	*/
	protected function runCmd($command)
	{
		ob_start();
		system($command, $return);
		ob_end_clean();
		return($return);
	}

	public function setUp()
	{
		parent::setUp();
		$this->config = new USVN_Config("www/USVN/config.ini", "general");
		$this->old_db_database = $this->config->database->options->dbname;
		$this->config->database->options->dbname = "usvn-test";
		$this->old_db_password = $this->config->database->options->password;
		$this->config->database->options->password = "usvn-test";
		$this->old_db_user = $this->config->database->options->username;
		$this->config->database->options->username = "usvn-test";
		$this->base_url = $this->config->url->base;
		$this->config->save();
		$this->hooks_url = "http://" . str_replace("//", "/", $this->config->url->host.$this->base_url."/project/love/svnhooks/");

		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			if (!file_exists('USVN')) {
				exec("ln -s www/USVN");
			}
			exec('rm -Rf tests/testclient');
			mkdir('tests/testclient');
			exec('svnadmin create tests/testclient/test');
			chmod('client/usvn', 0700);
			$this->assertEquals("", system("./client/usvn install tests/testclient/test {$this->hooks_url} 007", $return));
			$this->assertEquals(0, $return);
			$this->repository_path = str_replace('/client/..', '', dirname(__FILE__).'/../tests/testclient/test');
			exec('svn co file://' . $this->repository_path . ' tests/testclient/titi');
			file_put_contents('tests/testclient/titi/fichier1', "Fichier 1");
			file_put_contents('tests/testclient/titi/fichier2', "Fichier 2");
			$this->assertEquals(0, $this->runCmd('cd tests/testclient/titi && svn add fichier1 fichier2'));
			$this->assertEquals(0, $this->runCmd("cd tests/testclient/titi && svn commit -m 'Test de commit'"));
		}
	}

	public function tearDown()
	{
		$this->config->database->options->dbname = $this->old_db_database ;
		$this->config->database->options->password = $this->old_db_password;
		$this->config->database->options->username = $this->old_db_user;
		$this->config->save();
		parent::tearDown();
	}
}

// Call USVN_ClientTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_ClientTest::main") {
    USVN_ClientTest::main();
}
?>
