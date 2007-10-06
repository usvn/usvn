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
    define("PHPUnit_MAIN_METHOD", "menus_BrowserTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class menus_browserTest extends USVN_Test_Test {
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("menus_BrowserTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function test_getProjectSubMenu()
	{
		$http = new Zend_Controller_Request_Http();
		$http->setParam('project', 'test');
		$menu = new menus_browser($http, null);
		$menuEntries = $menu->getProjectSubMenu();
		$this->assertEquals(1, count($menuEntries));
        $this->assertContains(
        array(
            "title" => T_("Browser"),
            "link"=> "project/test/browser/",
            "controller" => "index",
            "action" => ""
        )
        , $menuEntries);
    }
}

if (PHPUnit_MAIN_METHOD == "menus_BrowserTest::main") {
    menus_BrowserTest::main();
}
?>
