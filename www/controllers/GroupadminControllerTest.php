<?php
/**
 * Group management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package admin
 * @subpackage group
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This group has been realised as part of
 * end of studies group.
 *
 * $Id$
 */

// Call GroupControllerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "GroupadminControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class GroupadminControllerTest extends USVN_Test_AdminController {
	protected $controller_name = "groupadmin";
	protected $controller_class = "GroupadminController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("GroupadminControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	protected function setUp()
	{
		parent::setUp();
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
		$this->assertEquals(2, count($this->controller->view->groups));
		$this->assertContains('Indochine', $this->getBody());
		$this->assertContains('Bob morane', $this->getBody());
	}

	public function test_updateNoPostData()
	{
		try {
			$this->runAction('update');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals('/admin/group/', $e->url);
		}
	}

	public function test_updateInvalidGroup()
	{
		$_POST['groups_name'] = 'Pif';
		$_POST["groups_description"] = '';
		$this->request->setParam('name', 'Pif');
		try {
			$this->runAction('update');
		}
		catch (USVN_Exception $e) {
			$this->assertContains('Pif', $e->getMessage());
			return;
		}
		$this->fail();
	}

	public function test_update()
	{
		$_POST['groups_name'] = 'Pif';
		$_POST["groups_description"] = 'Pif le chien';
		$_POST["users"] = array($this->user->id);
		$this->request->setParam('name', 'Indochine');
		try {
			$this->runAction('update');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals($e->url, '/admin/group/');
			$groups = new USVN_Db_Table_Groups();
			$group = $groups->fetchRow(array('groups_name = ?' => 'Pif'));
			$this->assertEquals('Pif le chien', $group->description);
			$this->assertTrue($group->hasUser($this->user));
			return;
		}
		$this->fail();
	}

	public function test_create()
	{
		$_POST['groups_name'] = 'Pif';
		$_POST["groups_description"] = 'Pif le chien';
		$_POST["users"] = array($this->user->id);
		try {
			$this->runAction('create');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals($e->url, '/admin/group/');
			$groups = new USVN_Db_Table_Groups();
			$group = $groups->fetchRow(array('groups_name = ?' => 'Pif'));
			$this->assertEquals('Pif le chien', $group->description);
			$this->assertTrue($group->hasUser($this->user));
			return;
		}
		$this->fail($this->getBody());
	}

	public function test_createGroupAlreadyExist()
	{
		$_POST['groups_name'] = 'Indochine';
		$_POST["groups_description"] = 'Pif le chien';
		$_POST["users"] = array($this->user->id);
		$this->runAction('create');
		$this->assertContains("Group Indochine already exist", $this->getBody());
	}

	public function test_delete()
	{
		$groups = new USVN_Db_Table_Groups();
		$this->assertNotNull($groups->fetchRow(array('groups_name = ?' => 'Indochine')));
		$this->request->setParam('name', 'Indochine');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals('/admin/group/', $e->url);
		}
		$this->assertNull($groups->fetchRow(array('groups_name = ?' => 'Indochine')));
	}

	public function test_deleteBadGroup()
	{
		$groups = new USVN_Db_Table_Groups();
		$this->request->setParam('name', 'Pif');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function test_editBadGroup()
	{
		$this->request->setParam('name', 'Pif');
		try {
			$this->runAction('edit');
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function test_edit()
	{
		$groups = new USVN_Db_Table_Groups();
		$group = $groups->find(43)->current();
		$group->addUser($this->user);
		$this->request->setParam('name', 'Indochine');
		$this->runAction('edit');
		$this->assertEquals(2, count($this->controller->view->users));
		$this->assertContains('Indochine', $this->getBody(), $this->getBody());
		$this->assertContains('<option value="' . $this->user->id.  '" selected="selected">', $this->getBody(), $this->getBody());
	}

	public function test_new()
	{
		$this->runAction('new');
		$this->assertEquals(2, count($this->controller->view->users));
	}
}

// Call GroupControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "GroupadminControllerTest::main") {
    GroupadminControllerTest::main();
}
?>
