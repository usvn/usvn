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

// Call IndexControllerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "IndexControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class IndexControllerTest extends USVN_Test_Controller {
	protected $controller_name = "index";
	protected $controller_class = "IndexController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("IndexControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function test_indexActionNoProject()
	{
		$this->runAction('index', 'index');
		$this->assertEquals(0, count($this->controller->view->projects));
		$this->assertContains("You don't have any project.", $this->getBody());
	}

	public function test_indexActionTwoProject()
	{
		$table_project = new USVN_Db_Table_Projects();
		$project = $table_project->fetchNew();
		$project->setFromArray(array('projects_name' => 'OpenBSD',  'projects_start_date' => '1984-12-03 00:00:00'));
		$project->save();
		$project->addUser($this->user);
		$project2 = $table_project->fetchNew();
		$project2->setFromArray(array('projects_name' => 'Hurd',  'projects_start_date' => '1984-12-03 00:00:00'));
		$project2->save();
		$project2->addUser($this->user);

		$this->runAction('index', 'index');
		$this->assertEquals(2, count($this->controller->view->projects));
		$this->assertContains("OpenBSD", $this->getBody());
		$this->assertContains("Hurd", $this->getBody());
	}
}

// Call IndexControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "IndexControllerTest::main") {
    IndexControllerTest::main();
}
?>
