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

class USVN_MenuTest extends USVN_Test_Test
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
	public static function getTopMenu($request)
	{
		return array(
			array(
				"title" => T_("Administration"),
				"link"=> "admin/"
			)
		);
	}

	public static function getSubMenu($request)
	{
		return array(
			array(
				"title" => T_("Users"),
				"link"=> "admin/users/"
			),
			array(
				"title" => T_("Groups"),
				"link"=> "admin/users/"
			),
			array(
				"title" => T_("Projects"),
				"link"=> "admin/projects/"
			),
			array(
				"title" => T_("Configuration"),
				"link"=> "admin/config/"
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
	public static function getTopMenu($request)
	{
		return array(
			array(
				"title" => T_("View tickets"),
				"link"=> "tickets/"
			),
			array(
				"title" => T_("My tickets"),
				"link"=> "tickets/my"
			)
		);
	}

	public static function getSubMenu($request)
	{
		return array(
			array(
				"title" => T_("New ticket"),
				"link"=> "tickets/new/"
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
		$menu = new USVN_Menu($this->moduledir, new Zend_Controller_Request_Http());
		$menuEntries = $menu->getTopMenu();
		$this->assertContains(array("title" => T_("Administration"), "link"=> "admin/"), $menuEntries);
		$this->assertContains(array("title" => T_("View tickets"), "link"=> "tickets/"), $menuEntries);
		$this->assertContains(array("title" => T_("My tickets"), "link"=> "tickets/my"), $menuEntries);
	}

	public function test_getSubMenu()
	{
		$request = new Zend_Controller_Request_Http();
		$request->setParam('module', 'fakeadmin');
		$menu = new USVN_Menu($this->moduledir, $request);
		$menuEntries = $menu->getSubMenu();
		$this->assertContains(array("title" => T_("Users"), "link"=> "admin/users/"), $menuEntries);
		$this->assertNotContains(array("title" => T_("New ticket"), "link"=> "tickets/new/"), $menuEntries);
	}
}

// Call USVN_MenuTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_MenuTest::main") {
    USVN_MenuTest::main();
}
?>
