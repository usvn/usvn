<?php
/**
 * USVN client
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

// Call USVN_ClientTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_ClientTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once "www/USVN/autoload.php";

class USVN_ClientTest extends USVN_Test_DB {
	private $config;
	private $base_url;
	private $old_db_user;
	private $old_db_password;
	private $old_db_database;


	public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_ClientTest");
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

	public function testinstallLinux()
	{
		/*
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			exec('rm -Rf /tmp/test');
			exec('svnadmin create /tmp/test');
			$this->assertEquals("", system('./client/usvn install /tmp/test http://localhost/$baseurl/project/love/svnhooks/ 007', $return));
			$this->assertEquals(0, $return);
			exec('rm -Rf /tmp/titi');
			exec('svn co file:///tmp/test /tmp/titi');
			chdir('/tmp/titi');
			file_put_contents('fichier1', "Fichier 1");
			file_put_contents('fichier2', "Fichier 2");
			system('svn add fichier1 fichier2', $return);
			$this->assertEquals(0, $return);
			system("svn commit -m 'Test de commit'", $return);
			$this->assertEquals(0, $return);
			system('svn lock fichier1', $return);
			$this->assertEquals(0, $return);
			system('svn unlock fichier1', $return);
			$this->assertEquals(0, $return);
			system('svn propset -r 1 --revprop svn:log "new log message"', $return);
		}*/
	}
}

// Call USVN_ClientTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_ClientTest::main") {
    USVN_ClientTest::main();
}
?>
