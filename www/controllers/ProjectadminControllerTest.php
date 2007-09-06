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

require_once 'www/USVN/autoload.php';

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
