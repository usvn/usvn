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

class USVN_ClientTest extends PHPUnit_Framework_TestCase {
	public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_ClientTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	protected function setUp()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			if (!file_exists('USVN')) {
				exec("ln -s www/USVN");
			}
		}
	}

	public function testinstallLinux()
	{
		if (!(substr(php_uname(), 0, 7) == "Windows")) {
			exec('rm -Rf /tmp/test');
//			exec('rm -Rf /tmp/titi');
			exec('svnadmin create /tmp/test');
//			exec('svn co file:///tmp/test /tmp/titi');
			$this->assertEquals("", system('./client/usvn install /tmp/test http://localhost/~noplay/usvn/www/project/love/svnhooks 007', $return));
			$this->assertEquals(0, $return);
/*			chdir('/tmp/titi');
			exec('touch fichier1');
			exec('touch fichier2');
			exec('svn add fichier1 fichier2');
			exec("svn commit -m 'Test de commit'");
			exec('svn lock fichier1');
			exec('svn unlock fichier1');
			exec('svn propset -r 1 --revprop svn:log "new log message"');*/
		}
	}
}

// Call USVN_ClientTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_ClientTest::main") {
    USVN_ClientTest::main();
}
?>
