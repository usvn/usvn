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
require_once "client/baseclientTest.php";

abstract class Abstract_USVN_ClientTest extends Abstract_USVN_BaseClientTest {
	public function setUp()
	{
		parent::setUp();

		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			exec('svnadmin create tests/testclient/test');
			$this->assertEquals("", system("./client/usvn install tests/testclient/test {$this->hooks_url} project-love 007", $return));
			$this->assertEquals(0, $return);
			$this->repository_path = str_replace('/client/..', '', dirname(__FILE__).'/../tests/testclient/test');
			exec('svn co file://' . $this->repository_path . ' tests/testclient/titi');
			file_put_contents('tests/testclient/titi/fichier1', "Fichier 1");
			file_put_contents('tests/testclient/titi/fichier2', "Fichier 2");
			$this->assertEquals(0, USVN_Client_ConsoleUtils::runCmd('cd tests/testclient/titi && svn add fichier1 fichier2'));
			$this->assertEquals(0, USVN_Client_ConsoleUtils::runCmd("cd tests/testclient/titi && svn commit -m 'Test de commit'"));
		}
	}
}

// Call USVN_ClientTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_ClientTest::main") {
    USVN_ClientTest::main();
}
?>
