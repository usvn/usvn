<?php
/**
 * Menu loader
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
// Call USVN_MenuTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_MenuTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class USVN_MenuTest extends USVN_Test_DB
{
	private $moduledir = 'tests/modules/';

    public static function main()
	{
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_MenuTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	protected function setUp()
	{
		parent::setUp();
		mkdir($this->moduledir);
		mkdir($this->moduledir."/fakeadmin");
		file_put_contents($this->moduledir."/fakeadmin/Menu.php", '
<?php
class USVN_modules_fakeadmin_Menu extends USVN_modules_default_AbstractMenu
{
	public static function getTopMenu($request, $identity)
	{
		return array(
			array(
				"title" => T_("Administration"),
				"link"=> "admin",
				"module" => "admin",
				"controller" => "index",
				"action" => ""
			)
		);
	}

	public static function getSubMenu($request, $identity)
	{
		return array(
			array(
				"title" => T_("Users"),
				"link"=> "admin/user/",
				"module" => "admin",
				"controller" => "user",
				"action" => ""
			),
			array(
				"title" => T_("Groups"),
				"link"=> "admin/group/",
				"module" => "group",
				"controller" => "index",
				"action" => ""
			),
			array(
				"title" => T_("Projects"),
				"link"=> "admin/project/",
				"module" => "admin",
				"controller" => "project",
				"action" => ""
			),
			array(
				"title" => T_("Configuration"),
				"link"=> "admin/config/",
				"module" => "admin",
				"controller" => "config",
				"action" => ""
			)
		);
	}
}
');
		mkdir($this->moduledir."/faketickets");
		file_put_contents($this->moduledir."/faketickets/Menu.php", '
<?php
class USVN_modules_faketickets_Menu extends USVN_modules_default_AbstractMenu
{
	public static function getTopMenu($request, $identity)
	{
		return array(
			array(
				"title" => T_("View tickets"),
				"link"=> "tickets/",
				"module" => "tickets",
				"controller" => "index",
				"action" => ""
			),
			array(
				"title" => T_("My tickets"),
				"link"=> "tickets/my",
				"module" => "tickets",
				"controller" => "index",
				"action" => ""
			)
		);
	}

	public static function getSubMenu($request, $identity)
	{
		return array(
			array(
				"title" => T_("New ticket"),
				"link"=> "tickets/new/",
				"module" => "tickets",
				"controller" => "index",
				"action" => ""
			)
		);
	}
}
');
	}

    protected function tearDown()
	{
		USVN_DirectoryUtils::removeDirectory($this->moduledir);
		parent::tearDown();
    }

	public function test_getTopMenu()
	{
	/*	$menu = new USVN_Menu($this->moduledir, new Zend_Controller_Request_Http(), null);
		$menuEntries = $menu->getTopMenu();
		$this->assertContains(
			array(
				"title" => T_("Administration"),
				"link"=> "admin",
				"module" => "admin",
				"controller" => "index",
				"action" => ""
			)
			, $menuEntries);
		$this->assertContains(array("title" => T_("View tickets"), "link"=> "tickets/"), $menuEntries);
		$this->assertContains(array("title" => T_("My tickets"), "link"=> "tickets/my"), $menuEntries);*/
	}
/*
	public function test_getSubMenu()
	{
		$request = new Zend_Controller_Request_Http();
		$request->setParam('module', 'fakeadmin');
		$menu = new USVN_Menu($this->moduledir, $request, null);
		$menuEntries = $menu->getSubMenu();
		$this->assertContains(
			array(
				"title" => T_("Users"),
				"link"=> "admin/user/",
				"module" => "admin",
				"controller" => "user",
				"action" => ""
			)
			, $menuEntries);
		$this->assertNotContains(array("title" => T_("New ticket"), "link"=> "tickets/new/"), $menuEntries);
	}*/
}

// Call USVN_MenuTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_MenuTest::main") {
    USVN_MenuTest::main();
}
?>
