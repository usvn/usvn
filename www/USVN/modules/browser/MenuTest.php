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
 * $Id: MenuTest.php 378 2007-05-06 19:27:52Z duponc_j $
 */
// Call USVN_modules_admin_MenuTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_modules_admin_MenuTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class USVN_modules_admin_MenuTest extends USVN_Test_Test {
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_modules_admin_MenuTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function test_getTopMenu()
	{
		$menu = new USVN_modules_admin_Menu();
		$menuEntries = $menu->getTopMenu(new Zend_Controller_Request_Http(), null);
		$this->assertContains(
			array(
				"title" => T_("Administration"),
				"link"=> "admin",
				"module" => "admin",
				"controller" => "index",
				"action" => ""
			), $menuEntries);
	}

	public function test_getSubMenu()
	{
		$menu = new USVN_modules_admin_Menu();
		$menuEntries = $menu->getSubMenu(new Zend_Controller_Request_Http(), null);
		$this->assertContains(
			array(
				"title" => T_("Configuration"),
				"link"=> "admin/config/",
				"module" => "admin",
				"controller" => "config",
				"action" => ""
			), $menuEntries);
	}

	public function test_getSubSubMenu()
	{
		$menu = new USVN_modules_admin_Menu();
		$request = new Zend_Controller_Request_Http();
		$request->setParam('controller', 'user');
		$menuEntries = $menu->getSubSubMenu($request, null);
		$this->assertContains(
			array(
				"title" => T_("Add new user"),
				"link"=> "admin/user/new/",
				"module" => "admin",
				"controller" => "user",
				"action" => "new"
			), $menuEntries);
	}
}

// Call USVN_modules_admin_MenuTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_modules_admin_MenuTest::main") {
    USVN_modules_admin_MenuTest::main();
}
?>
