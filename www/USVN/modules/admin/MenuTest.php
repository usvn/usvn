<?php
/**
 * Menu for admin module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage menu
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
// Call USVN_modules_admin_MenuTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_modules_admin_MenuTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class USVN_modules_admin_MenuTest extends PHPUnit_Framework_TestCase {
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_modules_admin_MenuTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function test_getTopMenu()
	{
		$menu = new USVN_modules_admin_Menu();
		$menuEntries = $menu->getTopMenu();
		$this->assertEquals("admin/", $menuEntries["Admin"]);
	}
}

// Call USVN_modules_admin_MenuTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_modules_admin_MenuTest::main") {
    USVN_modules_admin_MenuTest::main();
}
?>
