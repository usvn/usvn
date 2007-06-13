<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Bugs_395_Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class Bugs_395_Test extends USVN_Test_DB {
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Bugs_395_Test");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp() {
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

    public function testCreateProjectAndDeleteProjectGroup()
	{
		$project = USVN_Project::createProject(array('projects_name' => 'InsertProjectOk',  'projects_start_date' => '1984-12-03 00:00:00'), "test", true, true, true);

		$table = new USVN_Db_Table_Groups();
		$this->assertTrue($table->isAGroup('InsertProjectOk'), "Le groupe n'est pas cree");
		$group = $table->fetchRow(array("groups_name = ?" => 'InsertProjectOk'));

		$table = new USVN_Db_Table_GroupsToProjects();
		$groupstoprojects = $table->fetchRow(array("projects_id = ?" => $project->id, "groups_id = ?" => $group->id));
		$this->assertNotNull($groupstoprojects);

		$this->assertTrue($project->groupIsMember($group));

		$project->deleteGroup($group->id);

		$this->assertFalse($project->groupIsMember($group));
	}
}

if (PHPUnit_MAIN_METHOD == "Bugs_395_Test::main") {
    Bugs_395_Test::main();
}
?>
