<?php
/**
 * Menu for changeset module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage menu
 *a
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
// Call USVN_modules_changeset_MenuTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_modules_changeset_MenuTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class USVN_modules_changeset_MenuTest extends USVN_Test_Test {
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_modules_changeset_MenuTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function test_getTopMenuNoProject()
	{
		$menu = new USVN_modules_changeset_Menu();
		$request = new Zend_Controller_Request_Http();
		$request->setParam("project", '__NONE__');
		$menuEntries = $menu->getTopMenu($request, null);
		$this->assertEquals(array(), $menuEntries);
	}

	public function test_getTopMenuWithProject()
	{
		$menu = new USVN_modules_changeset_Menu();
		$request = new Zend_Controller_Request_Http();
		$request->setParam("project", 'test');
		$menuEntries = $menu->getTopMenu($request, null);
		$this->assertContains(
			array(
				"title" => T_("Changeset"),
				"link"=> "project/test/changeset/",
				"module" => "changeset",
				"controller" => "index",
				"action" => ""

			),
			$menuEntries);
	}
}

// Call USVN_modules_changeset_MenuTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_modules_changeset_MenuTest::main") {
    USVN_modules_changeset_MenuTest::main();
}
?>
