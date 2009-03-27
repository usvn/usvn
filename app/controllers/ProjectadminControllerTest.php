<?php
/**
 * Project management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage project
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call GroupControllerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ProjectadminControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'library/USVN/autoload.php';

class ProjectadminControllerTest extends USVN_Test_AdminController {
	protected $controller_name = "projectadmin";
	protected $controller_class = "ProjectadminController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ProjectadminControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	protected function setUp()
	{
		parent::setUp();
		$projects = new USVN_Db_Table_Projects();
		$projects->insert(
			array(
				"projects_id" => 42,
				"projects_name" => "Telephone",
				'projects_start_date' => '1984-12-03 00:00:00',
				"projects_description" => "test"
			)
		);
		$projects->insert(
			array(
				"projects_id" => 43,
				"projects_name" => "Indochine",
				'projects_start_date' => '1984-12-03 00:00:00',
				"projects_description" => "Bob morane"
			)
		);
		$groups = new USVN_Db_Table_Groups();
		$groups->insert(
			array(
				"groups_id" => 42,
				"groups_name" => "Telephone",
				"groups_description" => "test"
			)
		);
		$groups->insert(
			array(
				"groups_id" => 43,
				"groups_name" => "Indochine",
				"groups_description" => "Bob morane"
			)
		);
	}

	public function test_index()
	{
		$this->runAction('index');
		$this->assertEquals(2, count($this->controller->view->projects));
		$this->assertContains('Indochine', $this->getBody());
		$this->assertContains('Bob morane', $this->getBody());
	}

	public function test_new()
	{
		$this->runAction('new');
	}

	private function create_project()
	{
		$_POST["projects_name"] = 'Test';
		$_POST["projects_description"] = 'A test project';
		try {
			$this->runAction('create');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$projects = new USVN_Db_Table_Projects();
			$project = $projects->fetchRow(array('projects_name = ?' => 'Test'));
			$this->assertNotNull($project);
			$this->assertEquals($project->description, 'A test project');
			$this->assertEquals($project->name, 'Test');
			$this->assertEquals($e->url, '/admin/project/');
			return $project;
		}
		$this->fail($this->getBody());
	}

	private function get_created_group()
	{
		$groups = new USVN_Db_Table_Groups();
		return $groups->fetchRow(array('groups_name = ?' => 'Test'));
	}

	public function test_create()
	{
		$_POST["creategroup"] = 0;
		$_POST["addmetogroup"] = 0;
		$_POST["admin"] = 0;
		$_POST['createsvndir'] = 0;
		$project = $this->create_project();
		$this->assertFalse($project->userIsAdmin('god'));
		$this->assertNull($this->get_created_group());
		$this->assertEquals(0, count(USVN_SVNUtils::listSVN(Zend_Registry::get('config')->subversion->path . '/svn/Test', '/')));
	}

	public function test_createWithSVNDirectories()
	{
		$_POST["creategroup"] = 0;
		$_POST["addmetogroup"] = 0;
		$_POST["admin"] = 0;
		$_POST['createsvndir'] = 1;
		$project = $this->create_project();
		$this->assertFalse($project->userIsAdmin('god'));
		$this->assertNull($this->get_created_group());
		$this->assertEquals(
			array(
				array(
					'name' => 'branches',
					'isDirectory' => true,
					'path' => '/branches/'
				),
				array(
					'name' => 'tags',
					'isDirectory' => true,
					'path' => '/tags/'
				),
				array(
					'name' => 'trunk',
					'isDirectory' => true,
					'path' => '/trunk/'
				)
			), USVN_SVNUtils::listSVN(Zend_Registry::get('config')->subversion->path . '/svn/Test', '/'));
	}
	
	
	public function test_createUserIsAdmin()
	{
		$_POST["creategroup"] = 0;
		$_POST["addmetogroup"] = 0;
		$_POST["admin"] = 1;
		$_POST['createsvndir'] = 1;
		$project = $this->create_project();
		$this->assertTrue($project->userIsAdmin('god'));
		$this->assertNull($this->get_created_group());
	}

	public function test_createGroupe()
	{
		$_POST["creategroup"] = 1;
		$_POST["addmetogroup"] = 0;
		$_POST["admin"] = 0;
		$_POST['createsvndir'] = 1;
		$project = $this->create_project();
		$this->assertFalse($project->userIsAdmin('god'));
		$group = $this->get_created_group();
		$this->assertNotNull($group);
		$this->assertEquals('Test', $group->name);
		$this->assertEquals("Autocreated group for project Test", $group->description);
		$this->assertFalse($group->userIsGroupLeader($this->admin_user));
	}

	public function test_createGroupeWithLeader()
	{
		$_POST["creategroup"] = 1;
		$_POST["addmetogroup"] = 1;
		$_POST["admin"] = 0;
		$_POST['createsvndir'] = 1;
		$project = $this->create_project();
		$this->assertFalse($project->userIsAdmin('god'));
		$group = $this->get_created_group();
		$this->assertNotNull($group);
		$this->assertTrue($group->userIsGroupLeader($this->admin_user));
	}

	public function test_delete()
	{
		$projects = new USVN_Db_Table_Projects();
		$this->assertNotNull($projects->fetchRow(array('projects_name = ?' => 'Indochine')));
		$this->request->setParam('name', 'Indochine');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals('/admin/project/', $e->url);
		}
		$this->assertNull($projects->fetchRow(array('projects_name = ?' => 'Indochine')));
	}

	public function test_deleteBadProject()
	{
		$this->request->setParam('name', 'Pif');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}
}

// Call GroupControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ProjectadminControllerTest::main") {
    ProjectadminControllerTest::main();
}
?>
