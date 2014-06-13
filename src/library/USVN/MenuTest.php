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
 * $Id: MenuTest.php 1188 2007-10-06 12:03:17Z crivis_s $
 */
// Call USVN_MenuTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "USVN_MenuTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

define('CONFIG_FILE', 'tests/config.ini');

require_once 'library/USVN/autoload.php';

class USVN_MenuTest extends USVN_Test_DB
{
	private $_menudir = 'tests/menus/';

    public static function main()
	{
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("USVN_MenuTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	protected function setUp()
	{
		parent::setUp();
		mkdir($this->_menudir);
        file_put_contents($this->_menudir . "/beta.php", '
<?php
class menus_beta extends USVN_AbstractMenu
{
	public function getProjectSubMenu()
	{
		 $project = $this->_request->getParam("project");
            return array(
                  array(
                    "title" => "beta",
                    "link"=> "project/" . $project . "/beta/",
                    "controller" => "index",
                    "action" => ""
                )
            );
    }

	public function getGroupSubMenu()
	{
		 $group = $this->_request->getParam("group");
            return array(
                  array(
                    "title" => "beta",
                    "link"=> "group/" . $group . "/beta/",
                    "controller" => "index",
                    "action" => ""
                )
            );
	}

    public function getAdminSubMenu()
	{
		 $project = $this->_request->getParam("project");
            return array(
                  array(
                    "title" => "beta admin",
                    "link"=> "project/" . $project . "/beta/",
                    "controller" => "index",
                    "action" => ""
                )
            );
    }

    public function getSubSubMenu()
    {
        return (
            array(
            array(
				"title" => "Beta new user",
				"link"=> "admin/user/new/",
				"controller" => "beta",
				"action" => "new"
			)
            ));
    }
}
');
        require_once($this->_menudir . "/beta.php");
        file_put_contents($this->_menudir . "/alpha.php", '
<?php
class menus_alpha extends USVN_AbstractMenu
{
	public function getProjectSubMenu()
	{
		 $project = $this->_request->getParam("project");
            return array(
                  array(
                    "title" => "alpha",
                    "link"=> "project/" . $project . "/alpha/",
                    "controller" => "index",
                    "action" => ""
                )
            );
    }

	public function getGroupSubMenu()
	{
		 $group = $this->_request->getParam("group");
            return array(
                  array(
                    "title" => "alpha",
                    "link"=> "group/" . $group . "/alpha/",
                    "controller" => "index",
                    "action" => ""
                )
            );
	}

	public function getAdminSubMenu()
	{
		 $project = $this->_request->getParam("project");
            return array(
                  array(
                    "title" => "alpha admin",
                    "link"=> "project/" . $project . "/alpha/",
                    "controller" => "index",
                    "action" => ""
                )
            );
    }

    public function getSubSubMenu()
    {
        return (
            array(
            array(
				"title" => "Alpha new user",
				"link"=> "admin/user/new/",
				"controller" => "alpha",
				"action" => "new"
			)
            )
            );
    }
}
');
        require_once($this->_menudir . "/alpha.php");
    }

    protected function tearDown()
	{
		USVN_DirectoryUtils::removeDirectory($this->_menudir);
		parent::tearDown();
    }

	public function test_getProjectSubMenu()
	{
		$request = new Zend_Controller_Request_Http();
		$request->setParam('area', 'project');
		$request->setParam('project', 'bidon');
		$menu = new USVN_Menu($this->_menudir, $request, null);
		$menuEntries = $menu->getSubMenu();
		$this->assertContains(
			array(
                    "title" => "alpha",
                    "link"=> "project/bidon/alpha/",
                    "controller" => "index",
                    "action" => ""
			)
			, $menuEntries);
		$this->assertContains(
			array(
                    "title" => "beta",
                    "link"=> "project/bidon/beta/",
                    "controller" => "index",
                    "action" => ""
			)
			, $menuEntries);
	}

	public function test_getGroupSubMenu()
	{
		$request = new Zend_Controller_Request_Http();
		$request->setParam('area', 'group');
		$request->setParam('group', 'bidon');
		$menu = new USVN_Menu($this->_menudir, $request, null);
		$menuEntries = $menu->getSubMenu();
		$this->assertContains(
			array(
                    "title" => "alpha",
                    "link"=> "group/bidon/alpha/",
                    "controller" => "index",
                    "action" => ""
			)
			, $menuEntries);
		$this->assertContains(
			array(
                    "title" => "beta",
                    "link"=> "group/bidon/beta/",
                    "controller" => "index",
                    "action" => ""
			)
			, $menuEntries);
	}


	public function test_getAdminSubMenu()
	{
		$request = new Zend_Controller_Request_Http();
		$request->setParam('area', 'admin');
		$request->setParam('project', 'bidon');
		$menu = new USVN_Menu($this->_menudir, $request, null);
		$menuEntries = $menu->getSubMenu();
		$this->assertContains(
			array(
                    "title" => "alpha admin",
                    "link"=> "project/bidon/alpha/",
                    "controller" => "index",
                    "action" => ""
			)
			, $menuEntries);
		$this->assertContains(
			array(
                    "title" => "beta admin",
                    "link"=> "project/bidon/beta/",
                    "controller" => "index",
                    "action" => ""
			)
			, $menuEntries);
	}

	public function test_getSubSubMenu()
	{
		$request = new Zend_Controller_Request_Http();
		$request->setParam('project', 'bidon');
		$request->setParam('controller', 'alpha');
		$menu = new USVN_Menu($this->_menudir, $request, null);
		$menuEntries = $menu->getSubSubMenu();
		$this->assertContains(
            array(
				"title" => "Alpha new user",
				"link"=> "admin/user/new/",
				"controller" => "alpha",
				"action" => "new"
			)
			, $menuEntries);
		$this->assertNotContains(
			array(
				"title" => "Beta new user",
				"link"=> "admin/user/new/",
				"controller" => "beta",
				"action" => "new"
			)
			, $menuEntries);
	}
}

// Call USVN_MenuTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_MenuTest::main") {
    USVN_MenuTest::main();
}
?>
