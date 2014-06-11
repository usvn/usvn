<?php
/**
 * User management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage users
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call UserControllerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "UseradminControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'library/USVN/autoload.php';

class UseradminControllerTest extends USVN_Test_AdminController {
	protected $controller_name = "useradmin";
	protected $controller_class = "UseradminController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("UseradminControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	protected function setUp()
	{
		parent::setUp();
		$users = new USVN_Db_Table_Users();
		$users->insert(
			array(
				"users_id" => 42,
				"users_login" => "Telephone",
				"users_password" => "secret",
			)
		);
		$users->insert(
			array(
				"users_id" => 43,
				"users_login" => "Indochine",
				"users_password" => "secret",
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
	}

	public function test_index()
	{
		$this->runAction('index');
		$this->assertEquals(4, count($this->controller->view->users));
		$this->assertContains('Indochine', $this->getBody());
	}

	public function test_updateNoPostData()
	{
		try {
			$this->runAction('update');
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function test_update()
	{
		$_POST['users_login'] = 'Pif';
		$_POST['users_firstname'] = 'Pif le chien';
		$_POST['users_lastname'] = 'Hercule';
		$_POST['users_email'] = '';
		$_POST['users_password'] = '';
		$_POST['users_password2'] = '';
		$_POST['users_is_admin'] = false;

		$this->request->setParam('login', 'Indochine');
		try {
			$this->runAction('update');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals($e->url, '/admin/user/');
			$users = new USVN_Db_Table_Users();
			$user = $users->fetchRow(array('users_login = ?' => 'Pif'));
			$this->assertEquals('Pif le chien', $user->firstname);
			return;
		}
		$this->fail();
	}
	public function test_create()
	{
		$_POST['users_login'] = 'Pif';
		$_POST['users_firstname'] = 'Pif le chien';
		$_POST['users_lastname'] = 'Hercule';
		$_POST['users_email'] = '';
		$_POST['users_password'] = 'secretsecret';
		$_POST['users_password2'] = 'secretsecret';
		$_POST['users_is_admin'] = false;
		try {
			$this->runAction('create');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals($e->url, '/admin/user/');
			$users = new USVN_Db_Table_Users();
			$user = $users->fetchRow(array('users_login = ?' => 'Pif'));
			$this->assertEquals('Pif le chien', $user->firstname);
			return;
		}
		$this->fail($this->getBody());
	}

	public function test_createUserAlreadyExist()
	{
		$_POST['users_login'] = 'Indochine';
		$_POST['users_firstname'] = 'Pif le chien';
		$_POST['users_lastname'] = 'Hercule';
		$_POST['users_email'] = '';
		$_POST['users_password'] = 'secretsecret';
		$_POST['users_password2'] = 'secretsecret';
		$_POST['users_is_admin'] = false;
		$this->runAction('create');
		$this->assertContains("Login Indochine already exist", $this->getBody());
	}

	public function test_delete()
	{
		$users = new USVN_Db_Table_Users();
		$this->assertNotNull($users->fetchRow(array('users_login = ?' => 'Indochine')));
		$this->request->setParam('login', 'Indochine');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals('/admin/user/', $e->url);
		}
		$this->assertNull($users->fetchRow(array('users_login = ?' => 'Indochine')));
	}

	public function test_deleteBadUser()
	{
		$users = new USVN_Db_Table_Users();
		$this->request->setParam('login', 'Pif');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}

	public function test_deleteYourself()
	{
		$users = new USVN_Db_Table_Users();
		$this->request->setParam('login', 'god');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Exception $e) {
			$this->assertcontains("yourself", $e->getMessage());
			return;
		}
		$this->fail();
	}

	public function test_editBadUser()
	{
		$this->request->setParam('login', 'Pif');
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
		$users = new USVN_Db_Table_Users();
		$user = $users->find(3)->current();
		$this->request->setParam('login', 'Indochine');
		$this->runAction('edit');
		$this->assertContains('Indochine', $this->getBody(), $this->getBody());
	}

	public function test_new()
	{
		$this->runAction('new');
		$this->assertEquals(1, count($this->controller->view->groups));
	}
}

// Call UserControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "UseradminControllerTest::main") {
    UseradminControllerTest::main();
}
?>
