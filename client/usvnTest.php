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
    define("PHPUnit_MAIN_METHOD", "USVN_Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once "www/USVN/autoload.php";
require_once "client/clientTest.php";

class USVN_Test extends Abstract_USVN_ClientTest {
	public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_Test");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function testinstallLinux()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			$this->assertEquals(0, $this->runCmd('cd tests/testclient/titi &&svn lock fichier1'));
			$this->assertEquals(0, $this->runCmd('cd tests/testclient/titi && svn unlock fichier1'));
			$this->assertEquals(0, $this->runCmd('cd tests/testclient/titi && svn propset -r 1 --revprop svn:log "new log message"'));
		}
	}
}

// Call USVN_ClientTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_Test::main") {
    USVN_Test::main();
}
?>
