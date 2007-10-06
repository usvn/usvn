<?php
/**
 * Usefull methods for project management
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6
 * @package usvn
 * @subpackage Table
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
// Call USVN_ProjectsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
	define("PHPUnit_MAIN_METHOD", "USVN_ProjectsTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';
define('CONFIG_FILE', 'tests/config.ini');

class USVN_ProjectsTest extends USVN_Test_DB {
	private $_user;

	public static function main() {
		require_once "PHPUnit/TextUI/TestRunner.php";

		$suite  = new PHPUnit_Framework_TestSuite("USVN_ProjectsTest");
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	public function setUp()
	{
		parent::setUp();
    	$table = new USVN_Db_Table_Users();

		$this->_user = $table->fetchNew();
		$this->_user->setFromArray(array('users_login' 	=> 'test',
									'users_password' 	=> 'password',
									'users_firstname' 	=> 'firstname',
									'users_lastname' 	=> 'lastname',
									'users_email' 		=> 'email@email.fr'));
		$this->_user->save();
	}

	public function testCreateProjectWithGroupWithAdmin()
	{
		$project = USVN_Project::createProject(array('projects_name' => 'InsertProjectOk',  'projects_start_date' => '1984-12-03 00:00:00'), "test", true, true, true);

		$table = new USVN_Db_Table_Projects();
		$this->assertTrue($table->isAProject('InsertProjectOk'), "Le projet n'est pas cree");
		$this->assertTrue(USVN_SVNUtils::isSVNRepository('tests/tmp/svn/InsertProjectOk'), "Le repository n'est pas cree");

		$table = new USVN_Db_Table_Groups();
		$this->assertTrue($table->isAGroup('InsertProjectOk'), "Le groupe n'est pas cree");
		$group = $table->fetchRow(array("groups_name = ?" => 'InsertProjectOk'));

		$table = new USVN_Db_Table_GroupsToProjects();
		$groupstoprojects = $table->fetchRow(array("projects_id = ?" => $project->id, "groups_id = ?" => $group->id));
		$this->assertNotNull($groupstoprojects);

		$table = new USVN_Db_Table_FilesRights();
		$right = $table->fetchRow(array("files_rights_path = ?" => "/", "projects_id = ?" => $project->id));
		$this->assertNotNull($right, "La ligne pour les droits sur / n'a pas ete trouvee");

		$table = new USVN_Db_Table_GroupsToFilesRights();
		$rights = $table->fetchRow(array("files_rights_id = ?" => $right->id, "groups_id = ?" => $group->id));
		$this->assertNotNull($rights, "La ligne pour les droits du groupe n'a pas ete trouvee");
		$this->assertEquals(1, $rights->files_rights_is_readable, "Le groupe n'a pas la lecture");
		$this->assertEquals(1, $rights->files_rights_is_readable, "Le groupe n'a pas l'ecriture");

		$this->assertTrue($group->userIsMember($this->_user), "L'utilisateur n'est pas membre du groupe " . $group->name);
		$this->assertTrue($project->userIsAdmin($this->_user));
	}

	public function testCreateProjectWithGroupButNotGroupMemberWithAdmin()
	{
		$project = USVN_Project::createProject(array('projects_name' => 'InsertProjectOk',  'projects_start_date' => '1984-12-03 00:00:00'), "test", true, false, true);

		$table = new USVN_Db_Table_Projects();
		$this->assertTrue($table->isAProject('InsertProjectOk'), "Le projet n'est pas cree");
		$this->assertTrue(USVN_SVNUtils::isSVNRepository('tests/tmp/svn/InsertProjectOk'), "Le repository n'est pas cree");

		$table = new USVN_Db_Table_Groups();
		$this->assertTrue($table->isAGroup('InsertProjectOk'), "Le groupe n'est pas cree");
		$group = $table->fetchRow(array("groups_name = ?" => 'InsertProjectOk'));

		$table = new USVN_Db_Table_FilesRights();
		$right = $table->fetchRow(array("files_rights_path = ?" => "/", "projects_id = ?" => $project->id));
		$this->assertNotNull($right, "La ligne pour les droits sur / n'a pas ete trouvee");

		$table = new USVN_Db_Table_GroupsToFilesRights();
		$rights = $table->fetchRow(array("files_rights_id = ?" => $right->id, "groups_id = ?" => $group->id));
		$this->assertNotNull($rights, "La ligne pour les droits du groupe n'a pas ete trouvee");
		$this->assertEquals(1, $rights->files_rights_is_readable, "Le groupe n'a pas la lecture");
		$this->assertEquals(1, $rights->files_rights_is_readable, "Le groupe n'a pas l'ecriture");

		$this->assertFalse($group->userIsMember($this->_user), "L'utilisateur est membre du groupe " . $group->name);
		$this->assertTrue($project->userIsAdmin($this->_user));
	}

	public function testCreateProjectWithGroupWithoutAdmin()
	{
		$project = USVN_Project::createProject(array('projects_name' => 'InsertProjectOk',  'projects_start_date' => '1984-12-03 00:00:00'), "test", true, true, false);

		$table = new USVN_Db_Table_Projects();
		$this->assertTrue($table->isAProject('InsertProjectOk'), "Le projet n'est pas cree");
		$this->assertTrue(USVN_SVNUtils::isSVNRepository('tests/tmp/svn/InsertProjectOk'), "Le repository n'est pas cree");

		$table = new USVN_Db_Table_Groups();
		$this->assertTrue($table->isAGroup('InsertProjectOk'), "Le groupe n'est pas cree");
		$group = $table->fetchRow(array("groups_name = ?" => 'InsertProjectOk'));

		$table = new USVN_Db_Table_FilesRights();
		$right = $table->fetchRow(array("files_rights_path = ?" => "/", "projects_id = ?" => $project->id));
		$this->assertNotNull($right, "La ligne pour les droits sur / n'a pas ete trouvee");

		$table = new USVN_Db_Table_GroupsToFilesRights();
		$rights = $table->fetchRow(array("files_rights_id = ?" => $right->id, "groups_id = ?" => $group->id));
		$this->assertNotNull($rights, "La ligne pour les droits du groupe n'a pas ete trouvee");
		$this->assertEquals(1, $rights->files_rights_is_readable, "Le groupe n'a pas la lecture");
		$this->assertEquals(1, $rights->files_rights_is_readable, "Le groupe n'a pas l'ecriture");

		$this->assertTrue($group->userIsMember($this->_user));
		$this->assertFalse($project->userIsAdmin($this->_user));
	}

	public function testCreateProjectWithoutGroupWithoutAdmin()
	{
		$project = USVN_Project::createProject(array('projects_name' => 'InsertProjectOk',  'projects_start_date' => '1984-12-03 00:00:00'), "test", false, false, false);

		$table = new USVN_Db_Table_Projects();
		$this->assertTrue($table->isAProject('InsertProjectOk'), "Le projet n'est pas cree");
		$this->assertTrue(USVN_SVNUtils::isSVNRepository('tests/tmp/svn/InsertProjectOk'), "Le repository n'est pas cree");

		$table = new USVN_Db_Table_Groups();
		$this->assertFalse($table->isAGroup('InsertProjectOk'), "Le groupe est cree alors qu'il ne doit pas");
		$this->assertFalse($project->userIsAdmin($this->_user));
	}


	public function testCreateProjectWithGroupWithInvalidAdmin()
	{
		try {
			USVN_Project::createProject(array('projects_name' => 'InsertProjectBAD',  'projects_start_date' => '1984-12-03 00:00:00'), "fake", true, true, true);
		}
		catch (USVN_Exception $e){
			$table = new USVN_Db_Table_Projects();
			$this->assertFalse($table->isAProject('InsertProjectBAD'));
			return;
		}
		$this->fail();
	}

	public function testDeleteProject()
	{
		USVN_Project::createProject(array('projects_name' => 'InsertProjectOK',  'projects_start_date' => '1984-12-03 00:00:00'), "test", true, true, true);

		USVN_Project::deleteProject('InsertProjectOK');

		$table = new  USVN_Db_Table_Projects();
		$this->assertFalse($table->isAProject('InsertProjectOk'), "Le projet n'est pas supprime");
		$table_groups = new USVN_Db_Table_Groups();
		$this->assertFalse($table_groups->isAGroup('InsertProjectOk'),"Le groupe n'est pas supprime");
	}

}

// Call USVN_ProjectsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_ProjectsTest::main") {
	USVN_ProjectsTest::main();
}
?>